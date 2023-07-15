<?php
declare(strict_types=1);

namespace Trejjam\Configuration\Helper\Migration;

use Nextras\Migrations\Entities\File;
use Nextras\Migrations\IExtensionHandler;

final class DummyHandler implements IExtensionHandler
{
	public function execute(File $file)
	{
		return null;
	}
}
