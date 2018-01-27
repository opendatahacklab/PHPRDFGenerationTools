<?php
/**
 * An instance of org:Organization (see https://www.w3.org/TR/vocab-org/) inside an OWL ontoogy in RDF/XML serialization
 *
 * Copyright 2018 Cristiano Longo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Cristiano Longo 
 */
class RDFXMLOrganization{
	
	private $xmlDocument;
	public $xmlElement;

	/**
	 * Create an empty istance of org:Organization with the specified IRI and inside the specified 
	 * xml document. $xml must be an instance of DOMDocument and must contain a rdf:RDF (root) element. The element
 	 * representig the organization will be added as child of such a rdf:RDF element.
	 */
	public function __construct($xmlDocument, $iri){
		$this->xmlDocument=$xmlDocument;
		$this->xmlElement=$xmlDocument->createElement('org:Organization');
		$about=$this->xmlElement->setAttribute('rdf:about',$iri);
		$xmlDocument->documentElement->appendChild($this->xmlElement);
	}

	/**
	 * Get the namespaces which are expected to be set (aside with respective
	 * abbreviation prefixes) in the destination ontology.
	 * excluding the default ones
	 * rdf, rdfs and owl-
	 *
	 * @return a map prefix -> namespace
	 */
	public static function getRequiredNamespaces() {
		return array (
				'org' => 'http://www.w3.org/ns/org#',
				'foaf' => 'http://xmlns.com/foaf/0.1/' 
		);
	}
	
	/**
	 * Get the set of vocabulary iris to be imported in the target ontology
	 */
	public static function getRequiredVocabularies() {
		return array (
				'http://www.w3.org/ns/org#',
				'http://xmlns.com/foaf/spec/index.rdf' 
		);
	}

	/**
	 * Set the organization name
	 */
	public function addName($name){
		$nameElement = $this->xmlDocument->createElement('foaf:name');
		$this->xmlElement->appendChild($nameElement);
		$nameElement->appendChild($this->xmlDocument->createTextNode($name));
	}

	/**
	 * Set this organization as a suborganization of the specified one.
	 */
	public function setAsSuborganization($parentOrganizationIri){
		$suborgElement=$this->xmlDocument->createElement('org:subOrganizationOf');
		$this->xmlElement->appendChild($suborgElement);
		$suborgElement->setAttribute('rdf:resource',$parentOrganizationIri);
	}
}