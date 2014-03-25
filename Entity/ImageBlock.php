<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * ImageBlock
 */
class ImageBlock extends Block implements ImageInterface
{
    /**
     * File uploaded
     * 
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $file;

    /**
     * Path
     * 
     * @var string
     */
    protected $path;

    /**
     * Name
     * 
     * @var string
     */
    protected $name;

    /**
     * Alternative text
     * 
     * @var string
     */
    protected $alt;


    /**
     * Get id
     * 
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Image has file
     * 
     * @return boolean
     */
    public function hasFile()
    {
        return null !== $this->file;
    }

    /**
     * Get file
     * 
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return \Ekyna\Bundle\CmsBundle\Entity\ImageBlock
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        
        return $this;
    }

    /**
     * Image has path
     *
     * @return boolean
     */
    public function hasPath()
    {
        return null !== $this->path;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return \Ekyna\Bundle\CmsBundle\Entity\ImageBlock
     */
    public function setPath($path)
    {
        $this->path = $path;
        
        return $this;
    }

    /**
     * Image should be renamed
     *
     * @return boolean
     */
    public function shouldBeRenamed()
    {
        return (bool) ($this->hasPath() && $this->guessFilename() != pathinfo($this->getPath(), PATHINFO_BASENAME));
    }

    /**
     * Guess file name
     *
     * @return string
     */
    public function guessFilename()
    {
        // Extension
        $extension = null;
        if($this->hasFile()) {
            $extension = $this->file->guessExtension();
        }elseif($this->hasPath()) {
            $extension = pathinfo($this->getPath(), PATHINFO_EXTENSION);
        }
        
        // Filename
        $filename = null;
        if($this->hasName()) {
            $filename = pathinfo($this->name, PATHINFO_FILENAME);
        }elseif($this->hasFile()) {
            $filename = pathinfo($this->file->getFilename(), PATHINFO_FILENAME);
        }elseif($this->hasPath()) {
            $filename = pathinfo($this->path, PATHINFO_FILENAME);
        }
        
        if($filename !== null && $extension !== null) {
            return $filename.'.'.$extension;
        }
        
        return null;
    }

    /**
     * Image has name
     * 
     * @return boolean
     */
    public function hasName()
    {
        return (bool) (1 === preg_match('/^[a-z0-9-]+\.(jpg|jpeg|gif|png)$/', $this->name));
    }

    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->hasName() ? $this->name : $this->guessFilename();
    }

    /**
     * Set name
     * @param string $name
     * @return \Ekyna\Bundle\CmsBundle\Entity\ImageBlock
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }

    /**
     * Get alternative text
     * 
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set alternative text
     * 
     * @param string $alt
     * @return \Ekyna\Bundle\CmsBundle\Entity\ImageBlock
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'image';
    }
}
