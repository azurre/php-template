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
    /** @var string */
    protected $baseTemplatesPath = './';

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

    /** @var array */
    protected $includeData = [];

    /**
     * Engine constructor.
     *
     * @param string $baseTemplatesPath
     */
    public function __construct($baseTemplatesPath = null)
    {
        if ($baseTemplatesPath) {
            $this->setBaseTemplatePath($baseTemplatesPath);
        }
    }

    /**
     * @param string|null $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getData($key = null, $defaultValue = null)
    {
        if ($key === null) {
            return $this->data;
        }
        return $this->data[$key] ?? $defaultValue;
    }

    /**
     * @param string $baseTemplatesPath
     * @return $this
     */
    public function setBaseTemplatePath($baseTemplatesPath)
    {
        $this->baseTemplatesPath = $baseTemplatesPath;
        return $this;
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

    /** @param string $section */
    public function section($section)
    {
        $this->sectionEnd();
        $this->currentSection = $section;
    }

    /**
     * @param string $section
     * @return bool
     */
    public function hasSection($section)
    {
        return isset($this->sections[$section]);
    }

    /**
     * @param string|null $defaultTemplate
     * @return string
     * @throws Exception
     */
    public function getSection($section, $defaultTemplate = null)
    {
        if (!isset($this->sections[$section])) {
            if ($defaultTemplate && is_file($defaultTemplate)) {
                return $this->fetch($defaultTemplate);
            }
            return '';
        }
        return $this->sections[$section];
    }

    /**
     * @param string $template
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function include($template, $data = [])
    {
        $this->includeData = $data;
        $this->includeTemplate($template);
        $this->includeData = [];
    }

    /**
     * @param string $template
     * @throws Exception
     */
    protected function includeTemplate($template)
    {
        $path = $this->templatesPath . DIRECTORY_SEPARATOR . $template;
        if (is_file($path)) {
            extract($this->includeData, EXTR_OVERWRITE);
            include($path);
            return;
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
     * @param string $template
     * @return string|false
     * @throws Exception
     */
    public function fetch($template)
    {
        return $this->render($template);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        return $this->assign($key, $value);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function assign($key, $value)
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
        $path = $this->baseTemplatesPath . DIRECTORY_SEPARATOR . $template;
        if (is_file($path)) {
            extract($this->data, EXTR_OVERWRITE);
            include($this->baseTemplatesPath . DIRECTORY_SEPARATOR . $template);
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
        if ($this->layoutTemplate) {
            $path = $this->baseTemplatesPath . DIRECTORY_SEPARATOR . $this->layoutTemplate;
            if (is_file($path)) {
                extract(array_merge($this->layoutData), EXTR_OVERWRITE);
                include($path);
                $this->layoutTemplate = null;
                $this->layoutData = [];
                return ob_get_contents();
            }
            throw new Exception("Can't find {$path}");
        }
        return ob_get_contents();
    }

    /** @return void */
    protected function sectionEnd()
    {
        if ($this->currentSection !== null) {
            $this->sections[$this->currentSection] = ob_get_contents();
            $this->currentSection = null;
            ob_clean();
        }
    }
}
