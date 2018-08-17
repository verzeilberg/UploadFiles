<?php

namespace UploadFiles\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Files
 *
 * @ORM\Entity
 * @ORM\Table(name="files")
 */
class File {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=11, name="id");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Annotation\Required(false)
     * @Annotation\Type("Zend\Form\Element\File")
     * * @Annotation\Options({
     * "label": "Upload bestand",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     */
    private $file;

    /**
     * @ORM\Column(name="name_image", type="string", length=255, nullable=true)
     * @Annotation\Required(false)
     * @Annotation\Options({
     * "label": "Bestands naam",
     * "label_attributes": {"class": "col-sm-4 col-md-4 col-lg-4 control-label"}
     * })
     * @Annotation\Attributes({"class":"form-control", "placeholder":"Bestands naam"})
     */
    protected $nameFile;


    /**
     * @ORM\Column(name="sort_order", type="integer", length=11, nullable=true);
     * @Annotation\Required(false)
     */
    protected $sortOrder = 0;

    function getId() {
        return $this->id;
    }

    function getFile() {
        return $this->file;
    }

    function getNameFile() {
        return $this->nameFile;
    }

    function getSortOrder() {
        return $this->sortOrder;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setFile($file) {
        $this->file = $file;
    }

    function setNameFile($nameFile) {
        $this->nameFile = $nameFile;
    }

    function setSortOrder($sortOrder) {
        $this->sortOrder = $sortOrder;
    }




}
