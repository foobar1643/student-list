<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Utility
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Utility;

use Psr\Http\Message\ResponseInterface;

/**
 * Renders HTML templates into PSR-7 Response object.
 */
class View
{
    /**
     * Path to directory to load templates from.
     * @var string
     */
    protected $templatesDir;

    /**
     * Constructor.
     *
     * @param string $templatesDir Path to directory to load templates from.
     */
    public function __construct($templatesDir)
    {
        $this->templatesDir = $templatesDir;
    }

    /**
     * Renders a template with given name to Response object.
     *
     * @param string $templateName Name of the template to render.
     * @param ResponseInterface $response Response object to write a result.
     * @param array $vars Variables to render in the array in a form of a template.
     *
     * @return ResponseInterface Response object with rendered template.
     */
    public function renderTemplate($templateName, ResponseInterface $response, array $vars)
    {
        $body = $response->getBody();
        if(!$body->isWritable()) {
            throw new \RuntimeException('Response body must be writeable.');
        }
        ob_start();
        extract($vars, EXTR_PREFIX_SAME, "wddx");
        require("{$this->templatesDir}/{$templateName}");
        $body->write(ob_get_clean());
        ob_end_clean();
        return $response->withBody($body);
    }
}