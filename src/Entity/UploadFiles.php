<?php

namespace UploadFiles\Entity;

use Zend\Form\Annotation;
use Doctrine\ORM\Mapping as ORM;
use Application\Model\UnityOfWork;

/**
 * This class represents a uploadfiles item.
 * @ORM\Entity()
 * @ORM\Table(name="file")
 */
class UploadFiles extends UnityOfWork {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(name="subject", type="integer", length=11, nullable=false)
     */
    protected $size;
    
        /**
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     */
    protected $path;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Annotation\Options({
     * "label": "Omschrijving",
     * "label_attributes": {"class": "col-sm-1 col-md-1 col-lg-1 form-control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control"})
     */
    protected $description;

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getType() {
        return $this->type;
    }

    function getSize() {
        return $this->size;
    }

    function getDescription() {
        return $this->description;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setSize($size) {
        $this->size = $size;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function getPath() {
        return $this->path;
    }

    function setPath($path) {
        $this->path = $path;
    }

 
}
