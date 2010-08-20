<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/Export/classes/class.ilXmlImporter.php");

/**
 * Importer class for pages
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id: $
 * @ingroup ModulesMediaPool
 */
class ilCOPageImporter extends ilXmlImporter
{
	/**
	 * Import XML
	 *
	 * @param
	 * @return
	 */
	function importXmlRepresentation($a_entity, $a_id, $a_xml, $a_mapping)
	{
//echo $a_id;
//var_dump($a_xml);

		$pg_id = $a_mapping->getMapping("Services/COPage", "pg", $a_id);
		if ($pg_id != "")
		{
			$id = explode(":", $pg_id);
			if (count($id) == 2)
			{
				include_once("./Services/COPage/classes/class.ilPageObject.php");
				$new_page = new ilPageObject($id[0]);
				$new_page->setId($id[1]);
				$new_page->setXMLContent($a_xml);
				//$new_page->saveMobUsage($a_xml); (will be done in final processing)
				$new_page->createFromXML();
			}
		}
	}

	/**
	 * Final processing
	 *
	 * @param	array		mapping array
	 */
	function finalProcessing($a_mapping)
	{
		$pages = $a_mapping->getMappingsOfEntity("Services/COPage", "pg");
		$media_objects = $a_mapping->getMappingsOfEntity("Services/MediaObjects", "mob");

		if (count($media_objects) > 0)
		{
			foreach ($pages as $p)
			{
				$id = explode(":", $p);
				if (count($id) == 2)
				{
					include_once("./Services/COPage/classes/class.ilPageObject.php");
					$new_page = new ilPageObject($id[0], $id[1]);
					$new_page->buildDom();
					if ($new_page->resolveMediaAliases($media_objects))
					{
						$new_page->update(false, true);
					}
				}
			}
		}
	}
}

?>