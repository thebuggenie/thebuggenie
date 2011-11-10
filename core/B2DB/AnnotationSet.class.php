<?php

	namespace b2db;
	
	class AnnotationSet
	{

		protected static $_ignored_annotations = array('var', 'access', 'package', 'subpackage', 'author', 'license', 'verison', 'copyright');

		protected $_annotations = array();

		public function __construct($docblock)
		{
			$current_annotation = null;
			$current_annotation_data = null;
			$dblen = strlen($docblock);
			$annotations = array();
			for ($i = 0; $i < $dblen; $i++) {
				$character = $docblock[$i];
				if ($current_annotation === null) {
					if ($character == '@') {
						$current_annotation = '';
						$current_annotation_data = '';
						$i++;
						while (!$i <= $dblen) {
							$character = $docblock[$i];
							if (in_array($character, array("\n", " ", "("))) {
								break;
							} else {
								$current_annotation .= $character;
							}
							$i++;
						}
						if ($character == '(') {
							$i++;
							while (!$i <= $dblen) {
								$character = $docblock[$i];
								if (in_array($character, array("\n", ")"))) {
									break;
								} else {
									$current_annotation_data .= $character;
								}
								$i++;
							}

						}
						if (!in_array($current_annotation, self::$_ignored_annotations)) {
							$annotations[$current_annotation] = new Annotation($current_annotation, $current_annotation_data);
						}
						$current_annotation = null;
						$current_annotation_data = null;
					}
				}
			}
			$this->_annotations = $annotations;
		}

		/**
		 * Check to see if a specified annotation exists
		 *
		 * @param string $annotation
		 *
		 * @return boolean
		 */
		public function hasAnnotation($annotation)
		{
			return array_key_exists($annotation, $this->_annotations);
		}

		/**
		 * Returns the specified annotation
		 *
		 * @param string $annotation
		 *
		 * @return Annotation
		 */
		public function getAnnotation($annotation)
		{
			return ($this->hasAnnotation($annotation)) ? $this->_annotations[$annotation] : null;
		}

		/**
		 * Returns all annotations
		 *
		 * @return array|Annotation
		 */
		public function getAnnotations()
		{
			return $this->_annotations;
		}

		public function count()
		{
			return count($this->_annotations);
		}

		public function hasAnnotations()
		{
			return (bool) $this->count();
		}

	}
