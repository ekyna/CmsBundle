<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model\ImageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * ImageBlock
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
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
     * {@inheritdoc}
     */
    public function hasFile()
    {
        return null !== $this->file;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function hasPath()
    {
        return null !== $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBeRenamed()
    {
        return (bool) ($this->hasPath() && $this->guessFilename() != pathinfo($this->getPath(), PATHINFO_BASENAME));
    }

    /**
     * {@inheritdoc}
     */
    public function guessExtension()
    {
        if($this->hasFile()) {
            return $this->file->guessExtension();
        }elseif($this->hasPath()) {
            return pathinfo($this->getPath(), PATHINFO_EXTENSION);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function guessFilename()
    {
        // Extension
        $extension = $this->guessExtension();

        // Filename
        $filename = null;
        if($this->hasName()) {
            $filename = Urlizer::transliterate(pathinfo($this->name, PATHINFO_FILENAME)).'.'.$extension;;
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
