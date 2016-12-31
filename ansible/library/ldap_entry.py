#!/usr/bin/env python

DOCUMENTATION = """
---
module: ldap_entry
short_description: Creates, updates, or removes an LDAP entry.
description:
  - Creates, updates, or removes an LDAP entry in an LDAP directory.
version_added: 1.8.2
author: Branko Majic
notes:
  - Requires the python-ldap Python package on remote host. For Debian and
    derivatives, this is as easy as apt-get install python-ldap.
requirements:
  - python-ldap
options:
  dn:
    description:
      - Distinguished name of the entry.
    required: true
    default: ""
  state:
    description:
      - LDAP entry state. State C(present) requires that all entry attributes
        are listed. If you wish to append attributes to existing entry (or
        create a new one if it does not exist) use state C(append). If
        you wish to replace existing values for an attribute or create a new
        entry if it does not exist, use C(replace).
    required: true
    default: "present"
    choices: [ "present", "absent", "append", "replace" ]
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

  attributes:
    description:
      - Dictionary defining attributes used for the LDAP entry. This is an
        alternative way to provide entry attributes, and can be used alone or in
        conjunction with method described for "UNLISTED_OPTIONS" (see below).

  OTHER_OPTIONS:
    description:
      - All remaining options are considered to be attributes for an LDAP
        entry. LDAP schema constraints should be kept in mind (i.e. one
        structural objectClass etc). Attributes can be passed in as a simple
        string (for one value of an attribute), for storing multiple values for
        same attribute. If providing a base64-encoded value, prefix it with
        C(base64:) (this is useful for I(usercertificate;binary) or
        I(displayName) attributes). In order to remove an attribute, set its
        value to an empty string (C("")), and set the state to
        C(replace).
    required: false
    default: ""
"""

EXAMPLES = """
# Create sub-trees for storing user and group information.
ldap_entry: dn=ou=people,dc=example,dc=com objectClass=organizationalUnit ou=people
ldap_entry: dn=ou=groups,dc=example,dc=com objectClass=organizationalUnit ou=groups

# Remove old entries, using simple bind authentication.
ldap_entry: dn=ou=accounting,dc=example,dc=com state=absent bind_dn=cn=admin,dc=example,dc=com bind_password=foo123

# Create a complex entry that has multiple values for single attribute.
ldap_entry:
  dn: uid=john,ou=people,dc=example,dc=com
  objectClass:
    - inetOrgPerson
    - simpleSecurityObject
  uid: john
  cn: John Doe
  sn: Doe
  givenName: John
  displayName: base64:Sm9obiBEb2U=
  initials: JD
  mail: john.doe@example.com
  mobile: +1 11 111 111 11
  usercertificate;binary: base64:MIIC...lotsofcharacters...+/A==

# Add attribute to an entry.
ldap_entry:
  dn: uid=john,ou=people,dc=example,dc=com
  state: append
  mail: john.doe@example.com

# Make sure the configuration database has specific logging level enabled.
ldap_entry:
  dn: cn=config
  state: replace
  olcLogLevel: 256

# Remove attribute from an entry.
ldap_entry:
  dn: uid=john,ou=people,dc=example,dc=com
  state: replace
  uid: ""
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

from copy import deepcopy


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


class LDAPEntry(object):
    """
    Implements a convenience wrapper for managing an LDAP entry.
    """

    def __init__(self, dn, attributes, connection):
        """
        Initialises class instance, setting-up the necessary properties.

        Arguments:

        dn
          Distinguished name (DN) of an entry.

        attributes
          Attributes that should be set for an entry.

        connection
          An instance of LDAPObject class that will be used for running queries
          against an LDAP server.
        """

        self.connection = connection
        self.dn = dn
        self.attributes = attributes

    def add(self):
        """
        Adds entry to the LDAP directory.

        Returns:

        True, if entry was added, or had to be updated to match with requested
        attributes. False, if no change was necessary.
        """

        # If entry already exists with set attributes, only update it.
        if self.exists():
            return self._update()

        # Otherwise we need to add a new entry.
        self.connection.add_s(self.dn, ldap.modlist.addModlist(self.attributes))

        return True

    def remove(self):
        """
        Removes entry from an LDAP directory.

        Returns:

        True, if entry was removed. False if no change was necessary (entry is
        already not present).
        """

        if self.exists():
            self.connection.delete_s(self.dn)

            return True

        return False

    def append(self):
        """
        Append attributes to an existing entry. If the entry does not exist,
        create it.

        Returns:

        True, if entry was updated with new attribute values or if a new entry
        has been created. False if no change was necessary (values are already
        present).
        """

        if not self.exists():
            return self.add()

        attribute_list = self.attributes.keys()

        current_attributes = self.connection.search_s(self.dn, ldap.SCOPE_BASE, attrlist=attribute_list)[0][1]

        # This dictionary will contain all new attributes (or attribute values)
        # that should be added to the entry. We can't rely on modifyModlist
        # unfortunately, since if the values already exists, it will try to
        # remove and re-add them.
        new_attributes = {}

        # If attribute is already present, only add the difference between
        # requested and current values.
        for attribute, values in current_attributes.iteritems():
            if attribute in self.attributes:
                new_attributes[attribute] = [ item for item in self.attributes[attribute] if item not in values ]
            else:
                new_attributes[attribute] = values

        modification_list = ldap.modlist.modifyModlist({}, new_attributes)

        if not modification_list:
            return False

        self.connection.modify_s(self.dn, modification_list)

        return True

    def replace(self):
        """
        Replace attributes of an existing entry. If the entry does not exist,
        create it.

        Returns:

        True, if entry was updated with new attribute values or if a new entry
        has been created. False if no change was necessary (values are already
        present).
        """

        if not self.exists():
            return self.add()

        attribute_list = self.attributes.keys()

        current_attributes = self.connection.search_s(self.dn, ldap.SCOPE_BASE, attrlist=attribute_list)[0][1]

        modification_list = ldap.modlist.modifyModlist(current_attributes,
                                                       self.attributes, ignore_oldexistent=1)

        if not modification_list:
            return False

        self.connection.modify_s(self.dn, modification_list)

        return True

    def _update(self):
        """
        Updates an LDAP entry to have the requested attributes.

        Returns:

        True, if LDAP entry was updated. False if no change was necessary (entry
        already has the correct attributes).
        """

        self.current_attributes = self.connection.search_s(self.dn, ldap.SCOPE_BASE)[0][1]


        modification_list = ldap.modlist.modifyModlist(self.current_attributes,
                                                       self.attributes)

        if not modification_list:
            return False

        self.connection.modify_s(self.dn, modification_list)

        return True

    def exists(self):
        """
        Checks if the entry already exists in LDAP directory or not.

        Returns:
        True, if entry exists. False otherwise.
        """

        try:
            self.connection.search_s(self.dn, ldap.SCOPE_BASE, attrlist=["dn"])
        except ldap.NO_SUCH_OBJECT:
            return False

        return True


def main():
    """
    Runs the module.
    """

    # Construct the module helper for parsing the arguments.
    module = AnsibleModule(
        argument_spec=dict(
            dn=dict(required=True),
            state=dict(required=False, choices=["present", "absent", "append", "replace"], default="present"),
            server_uri=dict(required=False, default="ldapi:///"),
            bind_dn=dict(required=False, default=None),
            bind_password=dict(required=False),
            attributes=dict(required=False, type='dict', default=None),
            ),
        check_invalid_arguments=False
        )

    if not ldap_found:
        module.fail_json(msg="The Python LDAP module is required")

    # Extract the attributes. If a single value is provided for an attribute, it
    # must be convereted into one-element list. All items must be converted into
    # UTF-8 strings otherwise.
    attributes = {}

    def repack_value(value):
        """
        Small helper to repack a single value into list of UTF-8-encoded
        strings.
        """

        if isinstance(value, list):
            value = [ str(i).encode("utf-8") for i in value ]
        else:
            value = [ str(value).encode("utf-8") ]

        return value

    if module.params["attributes"]:
        for name, value in module.params["attributes"].iteritems():
            attributes[name] = repack_value(value)

    for name, value in module.params.iteritems():
        if name not in module.argument_spec:
            attributes.setdefault(name, []).extend(repack_value(value))
            attributes[name] = list(set(attributes[name]))
    try:
        connection = get_ldap_connection(module.params["server_uri"],
                                         module.params["bind_dn"],
                                         module.params["bind_password"])
    except ldap.LDAPError as e:
        module.fail_json(msg="LDAP error: %s" % str(e))
    state = module.params["state"]

    entry = LDAPEntry(module.params["dn"],
                      attributes,
                      connection)

    # Add/remove entry as requested.
    try:
        if state == "present":
            changed = entry.add()
        elif state == "append":
            changed = entry.append()
        elif state == "replace":
            changed = entry.replace()
        else:
            changed = entry.remove()
    except ldap.LDAPError as e:
        module.fail_json(msg="LDAP error: %s" % str(e))

    module.exit_json(changed=changed)


# Import module snippets.
from ansible.module_utils.basic import *
main()
