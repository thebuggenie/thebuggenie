#!/usr/bin/env python

DOCUMENTATION = """
---
module: ldap_permissions
short_description: Sets permissions/ACL for LDAP database.
description:
  - Sets permissions (access control list) for LDAP database.
version_added: 1.8.2
author: Branko Majic
notes:
  - Requires the python-ldap Python package on remote host. For Debian and
    derivatives, this is as easy as apt-get install python-ldap.
requirements:
  - python-ldap
options:
  filter:
    description:
      - LDAP filter that should be used for locating the database on which the
        ACL rules should be applied. This filter will be used for search under
        the C(cn=config) base DN. For regular user databases, the filter should
        probably be based on the C(olcSuffix) attribute. The filter must result
        in a unique entry.
    required: true
    default: ""
  rules:
    description:
      - LDAP rules that should be applied to the LDAP database. The rules should
        be provided as a list of strings. Each string should be an access rule
        as described in OpenLDAP administrator guide at
        U(http://www.openldap.org/doc/admin24/access-control.html). Use long
        format for specifying this parameter (see examples below).
    required: true
    default: ""
  server_uri:
    description:
      - LDAP connection URI specifying what server to connect to.
    required: false
    default: "ldapi:///"
  bind_dn:
    description:
      - DN for binding to the LDAP server using simple bind. If not set,
        EXTERNAL SASL binding method will be used.
    required: false
    default: ""
  bind_password:
    description:
      - Password for binding to the LDAP server using simple bind.
    required: false
    default: ""
"""

EXAMPLES = """
# Set-up of rules for regular database.
ldap_permissions:
  - filter: '(olcSuffix=dc=example,dc=com)'
    rules:
      - >
        to *
        by dn.exact=gidNumber=0+uidNumber=0,cn=peercred,cn=external,cn=auth manage
        by * break
      - >
        to attrs=userPassword,shadowLastChange
        by self write
        by anonymous auth
        by dn="cn=admin,dc=example,dc=com" write
        by * none
      - >
        to dn.base=""
        by * read
      - >
        to *
        by self write
        by dn="cn=admin,dc=example,dc=com" write
        by * none
# Set-up rules for a configuration database. This time with a single rule in a
# single line.
ldap_permissions:
  - filter: '(olcDatabase={0}config)'
    rules:
      - to * by dn.exact=gidNumber=0+uidNumber=0,cn=peercred,cn=external,cn=auth manage by * break
# Set-up rules on a remote server.
ldap_permissions:
  - filter: '(olcSuffix=dc=example,dc=com)'
    rules:
      - >
        to *
        by dn.exact=gidNumber=0+uidNumber=0,cn=peercred,cn=external,cn=auth manage
        by * break
      - >
        to attrs=userPassword,shadowLastChange
        by self write
        by anonymous auth
        by dn="cn=admin,dc=example,dc=com" write
        by * none
      - >
        to dn.base=""
        by * read
      - >
        to *
        by self write
        by dn="cn=admin,dc=example,dc=com" write
        by * none
    server_uri: ldap://ldap.example.com
    bind_dn: cn=admin,dc=example,dc=com
    bind_password: somepassword
"""

from ansible.module_utils.basic import *

# Try to load the Python LDAP module.
try:
    import ldap
    import ldap.sasl
    import ldap.modlist
except ImportError:
    ldap_found = False
else:
    ldap_found = True


def get_ldap_connection(uri, bind_dn=None, bind_password=""):
    """
    Connects and binds to an LDAP server.

    Arguments:

    uri
      LDAP connection URI specifying what server to connect to, including the
      protocol.

    bind_dn
      Distinguished name to be used for simple bind. If not set, SASL EXTERNAL
      mechanism will be used for log-in. Default is None.

    bind_password
      Password to be used for simple bind. Needs to be set only if bind_dn is
      set as well. Default is "".

    Returns:

    LDAP connection object.
    """

    connection = ldap.initialize(uri)

    if bind_dn:
        connection.simple_bind_s(bind_dn, bind_password)
    else:
        connection.sasl_interactive_bind_s("", ldap.sasl.external())

    return connection


class DatabaseFilteringError(Exception):
    """
    Exception intended to be thrown in case the filter passed in to module did
    not match one and only one entry in the configuration database.
    """
    pass


class LDAPPermissions(object):
    """
    Implements a convenience wrapper for managing permissions in OpenLDAP
    server.
    """

    def __init__(self, ldap_filter, rules, connection):
        """
        Initialises class instance, setting-up the necessary properties.

        Arguments:

        ldap_filter
          Filter that should be used under base cn=config to locate the database
          that should be modified.

        rules
          Rules to apply.

        connection
          LDAP connection object instance. This connection will be used for
          running queries against an LDAP server.
        """

        self.ldap_filter = ldap_filter
        self.rules = rules
        self.connection = connection

    def _get_database(self):
        """
        Retrieves the requested database entry.

        Returns:

        Database entry. Return format is same as for function ldap.search_s.
        """

        return self.connection.search_s(base="cn=config",
                                        scope=ldap.SCOPE_ONELEVEL,
                                        filterstr=self.ldap_filter)

    def _get_modifications(self, database):
        """
        Returns modification list for updating the current ACL with requested
        ACL.

        Returns:

        Modification list. The format is suitable for use with functions
        ldap.modify() and ldap.modify_s(). An empty list will be returned if no
        changes are necessary.
        """

        # Fetch the list of current rules.
        current_rules = database[1].get("olcAccess", [])

        # Set-up list of requested rules.
        requested_rules = []
        for n, rule in enumerate(self.rules):
            rule = "{%d}%s" % (n, rule)
            requested_rules.append(rule.rstrip().lstrip().encode("utf-8"))

        return ldap.modlist.modifyModlist({'olcAccess': current_rules}, {'olcAccess': requested_rules})

    def update(self):
        """
        Updates permissions for an LDAP database.

        Returns:

        True, if an update was performed, False if no update was necessary.
        """

        # Fetch the database config based on filter and verify and only one was
        # returned.
        databases = self._get_database()

        if databases == []:
            raise DatabaseFilteringError("No database matched filter: %s" % self.ldap_filter)
        elif len(databases) > 1:
            raise DatabaseFilteringError("More than one databases matched filter: %s" % self.ldap_filter)

        database = databases[0]

        # Set-up the modification list.
        modify_list = self._get_modifications(database)

        # Apply modifications if necessary.
        if modify_list == []:
            return False
        else:
            self.connection.modify_s(database[0], modify_list)
            return True


def main():
    """
    Runs the module.
    """

    # Construct the module helper for parsing the arguments.
    module = AnsibleModule(
        argument_spec=dict(
            filter=dict(required=True),
            rules=dict(required=True, type='list'),
            server_uri=dict(required=False, default="ldapi:///"),
            bind_dn=dict(required=False, default=None),
            bind_password=dict(required=False)
            )
        )

    if not ldap_found:
        module.fail_json(msg="The Python LDAP module is required")

    try:
        connection = get_ldap_connection(module.params["server_uri"],
                                         module.params["bind_dn"],
                                         module.params["bind_password"])
    except ldap.LDAPError as e:
        module.fail_json(msg="LDAP error: %s" % str(error_message))

    ldap_permissions = LDAPPermissions(module.params["filter"],
                                       module.params["rules"],
                                       connection)

    try:
        changed = ldap_permissions.update()

    except ldap.LDAPError as e:
        module.fail_json(msg="LDAP error: %s" % str(e))

    except DatabaseFilteringError as e:
        module.fail_json(msg="Module error: %s" % str(e))

    module.exit_json(changed=changed)

# Import module snippets.
from ansible.module_utils.basic import *
main()
