<?php

namespace Ekyna\Bundle\CmsBundle\Search\Wide;

/**
 * Class Result
 * @package Ekyna\Bundle\CmsBundle\Search\Wide
 * @author ekyna
 */
class Result
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var string
     */
    private $description;

    /**
     * @var number
     */
    private $score;

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return Result
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns the route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Sets the route.
     *
     * @param string $route
     *
     * @return Result
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Returns the parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the parameters.
     *
     * @param array $parameters
     *
     * @return Result
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description.
     *
     * @param string $description
     *
     * @return Result
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Returns the score.
     *
     * @return number
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Sets the score.
     *
     * @param number $score
     *
     * @return Result
     */
    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }
}
