<?php
/**
 * An instance of earl:Assertion (see https://www.w3.org/TR/EARL10-Schema) inside an OWL ontoogy in RDF/XML serialization
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
class RDFXMLEARLAssertion{
	
	public $xmlElement;
 
	/**
	 * Create an empty istance of earl:Assertion with the specified IRI and inside the specified 
	 * xml document. $xml must be an instance of DOMDocument and must contain a rdf:RDF (root) element. The element
 	 * representig the organization will be added as child of such a rdf:RDF element.
	 * 
	 * @param ontology RDFXMLOntology
	 */
	function __construct($ontology, $iri){
		$this->xmlDocument=$ontology->getXML();
		$this->xmlElement=$ontology->addIndividual('earl:Assertion', $iri);
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
				'earl' => 'http://www.w3.org/ns/earl#',
				'dct' => 'http://purl.org/dc/terms/' 
		);
	}
	
	/**
	 * Get the set of vocabulary iris to be imported in the target ontology
	 */
	public static function getRequiredVocabularies() {
		return array (
				'https://www.w3.org/ns/earl' 
		);
	}

	/**
	 * Attach an object property to this resource
	 */
	private function addObjectProperty($propertyiri,$objectiri){
		$propertyEl=$this->xmlDocument->createElement($propertyiri);
		$this->xmlElement->appendChild($propertyEl);
		$propertyEl->setAttribute('rdf:resource',$objectiri);
	}

	/**
	 * Set the test subject 
	 */
	public function setSubject($subjectiri){
		$this->addObjectProperty('earl:subject',$subjectiri);
	}

	/**
	 * Set the test
	 */
	public function setTest($testiri){
		$this->addObjectProperty('earl:test',$testiri);
	}

	/**
	 * Attach the result
	 */
	public function setResult($resultiri){
		$this->addObjectProperty('earl:result',$resultiri);
	}
}

class RDFXMLEARLTestCase{
	public $xmlElement;
 	public $iri;

	/**
	 * Create a empty istance of earl:TestCase 	
	 */
	function __construct($ontology, $iri){
		$this->xmlElement=$ontology->addIndividual('earl:TestCase',$iri);
		$this->iri=$iri;
	}
}

class RDFXMLEARLTestResult{
	public static $PASSED_OUTCOME_IRI='http://www.w3.org/ns/earl#passed';
	public static $FAILED_OUTCOME_IRI='http://www.w3.org/ns/earl#failed';

	public $xmlElement;
 	public $iri;
	public $outcomeiri;
	private $ontology;

	/**
	 * Create a empty istance of earl:TestResult
	 *
	 * @param $datetime DateTime when  object the test has been performed
	 */
	function __construct($ontology, $iri,$outcomeiri,$datetime){
		$this->ontology=$ontology;
		$this->xmlElement=$ontology->addIndividual('earl:TestResult',$iri);
		$this->iri=$iri;
		$this->setOutcome($outcomeiri);
		$this->setDate(isset($datetime) ? $datetime : new DateTime());
	}

	/**
	  * set the outcome
	 */
	private function setOutcome($outcomeiri){
		$outcomeP=$this->ontology->getXML()->createElement('earl:outcome');
		$this->xmlElement->appendChild($outcomeP);
		$outcomeP->setAttribute('rdf:resource',$outcomeiri);
		$this->outcomeiri=$outcomeiri;
   	}

	private function setDate($date){
		$xml=$this->ontology->getXML();
		$propertyEl=$xml->createElement('dct:date');
		$this->xmlElement->appendChild($propertyEl);
		$propertyEl->setAttribute('rdf:type','http://www.w3.org/2001/XMLSchema#date');
		$dateEl=$xml->createTextNode($date->format('Y-m-d'));
		$propertyEl->appendChild($dateEl);
	}
	/**
	 * Return true if the test is passed
	 */
	public function isPassed(){
		return $this->outcomeiri===self::$PASSED_OUTCOME_IRI;
	}

	/**
	 * Return true if the test is failed
	 */
	public function isFailed(){
		return $this->outcomeiri===self::$FAILED_OUTCOME_IRI;
	}
}