<?php
/**
 * Class Renderer
 *
 * Renderer implementation for file dropzones.
 *
 * @author  nmaerchy <nm@studer-raimann.ch>
 * @date    05.05.17
 * @version 0.0.9
 *
 * @package ILIAS\UI\Implementation\Component\Dropzone\File
 */

namespace ILIAS\UI\Implementation\Component\Dropzone\File;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\DefaultRenderer;
use ILIAS\UI\Implementation\Render\AbstractComponentRenderer;
use ILIAS\UI\Implementation\Render\ResourceRegistry;

class Renderer extends AbstractComponentRenderer {

	/**
	 * @var $renderer DefaultRenderer
	 */
	private $renderer;


	/**
	 * @inheritDoc
	 */
	protected function getComponentInterfaceName() {
		return array(
			\ILIAS\UI\Component\Dropzone\File\Standard::class,
			\ILIAS\UI\Component\Dropzone\File\Wrapper::class,
		);
	}


	/**
	 * @inheritdoc
	 */
	public function render(Component $component, \ILIAS\UI\Renderer $default_renderer) {
		$this->checkComponent($component);

		$this->renderer = $default_renderer;

		if ($component instanceof \ILIAS\UI\Component\Dropzone\File\Wrapper) {
			return $this->renderWrapperDropzone($component);
		}

		if ($component instanceof \ILIAS\UI\Component\Dropzone\File\Standard) {
			return $this->renderStandardDropzone($component);
		}
	}


	/**
	 * @inheritDoc
	 */
	public function registerResources(ResourceRegistry $registry) {
		parent::registerResources($registry);
		$registry->register("./src/UI/templates/js/Dropzone/File/dropzone-behavior.js");
		$registry->register("./src/UI/templates/js/libs/jquery.dragster.js");
	}


	/**
	 * Renders the passed in standerd dropzone.
	 *
	 * @param \ILIAS\UI\Component\Dropzone\File\Standard $standardDropzone the
	 *                                                                     dropzone
	 *                                                                     to
	 *                                                                     render
	 *
	 * @return string the html representation of the passed in argument.
	 */
	private function renderStandardDropzone(\ILIAS\UI\Component\Dropzone\File\Standard $standardDropzone) {

		$dropzoneId = $this->createId();

		// setup javascript
		$jsDropzoneInitializer = new JSDropzoneInitializer(SimpleDropzone::of()->setId($dropzoneId)->setType(\ILIAS\UI\Component\Dropzone\File\Standard::class)->setDarkenedBackground($standardDropzone->isDarkenedBackground())->setRegisteredSignals($standardDropzone->getTriggeredSignals()));

		$this->getJavascriptBinding()->addOnLoadCode($jsDropzoneInitializer->initDropzone());

		// setup template
		$tpl = $this->getTemplate("tpl.standard-dropzone.html", true, true);
		$tpl->setVariable("ID", $dropzoneId);

		// set message if not empty
		if (strcmp($standardDropzone->getMessage(), "") !== 0) {
			$tpl->setCurrentBlock("with_message");
			$tpl->setVariable("MESSAGE", $standardDropzone->getMessage());
			$tpl->parseCurrentBlock();
		}

		return $tpl->get();
	}


	/**
	 * Renders the passed in wrapper dropzone.
	 *
	 * @param \ILIAS\UI\Component\Dropzone\File\Wrapper $wrapperDropzone the
	 *                                                                   dropzone
	 *                                                                   to
	 *                                                                   render
	 *
	 * @return string the html representation of the passed in argument.
	 */
	private function renderWrapperDropzone(\ILIAS\UI\Component\Dropzone\File\Wrapper $wrapperDropzone) {

		$dropzoneId = $this->createId();

		// setup javascript
		$jsDropzoneInitializer = new JSDropzoneInitializer(SimpleDropzone::of()->setId($dropzoneId)->setType(\ILIAS\UI\Component\Dropzone\File\Wrapper::class)->setDarkenedBackground($wrapperDropzone->isDarkenedBackground())->setRegisteredSignals($wrapperDropzone->getTriggeredSignals()));

		$this->getJavascriptBinding()->addOnLoadCode($jsDropzoneInitializer->initDropzone());

		// setup template
		$tpl = $this->getTemplate("tpl.wrapper-dropzone.html", true, true);
		$tpl->setVariable("ID", $dropzoneId);
		$tpl->setVariable("CONTENT", $this->renderer->render($wrapperDropzone->getContent()));

		return $tpl->get();
	}
}