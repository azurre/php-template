<?php
/**
 * @author Alex Milenin
 * @email admin@azrr.info
 * @copyright Copyright (c)Alex Milenin (https://azrr.info/)
 */

namespace Azurre\Template;

/**
 * Class Engine
 */
class Engine
{
    protected $templatesPath;

    /** @var string */
    protected $layoutTemplate;

    /** @var string */
    protected $currentSection;

    /** @var array */
    protected $sections = [];

    /** @var array */
    protected $data = [];

    /** @var array */
    protected $layoutData = [];

    /**
     * Engine constructor.
     *
     * @param string $templatesPath
     */
    public function __construct($templatesPath)
    {
        $this->templatesPath = $templatesPath;
    }

    /**
     * @param string $template
     * @param array $data
     * @return $this
     */
    public function layout($template, array $data = [])
    {
        $this->layoutTemplate = $template;
        $this->layoutData = $data;
        return $this;
    }

    /**
     * @param string $section
     */
    public function section($section)
    {
        $this->sectionEnd();
        $this->currentSection = $section;
    }

    /**
     * @param string $section
     * @return string
     * @throws \Exception
     */
    public function getSection($section)
    {
        if (!isset($this->sections[$section])) {
            throw new Exception("Section {$section} not found");
        }
        return $this->sections[$section];
    }

    /**
     * @param string $template
     * @return void
     * @throws Exception
     */
    public function include($template)
    {
        $path = $this->templatesPath . DIRECTORY_SEPARATOR . $template;
        if (is_file($path)) {
            include($this->templatesPath . DIRECTORY_SEPARATOR . $template);
        }
        throw new Exception("Can't find {$path}");
    }

    /**
     * @param string $template
     * @param array $data
     * @return false|string
     * @throws Exception
     */
    public function render($template, $data = [])
    {
        ob_start();
        $this->data = array_merge($this->data, $data);
        $this->renderTemplate($template);
        $this->sectionEnd();
        $html = $this->renderLayout();
        ob_clean();
        return $html;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param string $template
     * @return void
     * @throws Exception
     */
    protected function renderTemplate($template)
    {
        $path = $this->templatesPath . DIRECTORY_SEPARATOR . $template;
        if (is_file($path)) {
            extract($this->data, EXTR_OVERWRITE);
            include($this->templatesPath . DIRECTORY_SEPARATOR . $template);
            return;
        }
        throw new Exception("Can't find {$path}");
    }

    /**
     * @return false|string
     * @throws Exception
     */
    protected function renderLayout()
    {
        $path = $this->templatesPath . DIRECTORY_SEPARATOR . $this->layoutTemplate;
        if (is_file($path)) {
            extract(array_merge($this->layoutData), EXTR_OVERWRITE);
            include($path);
            return ob_get_contents();
        }
        throw new Exception("Can't find {$path}");
    }

    /**
     * @return void
     */
    protected function sectionEnd()
    {
        if ($this->currentSection !== null) {
            $this->sections[$this->currentSection] = ob_get_contents();
            $this->currentSection = null;
            ob_clean();
        }
    }
}
