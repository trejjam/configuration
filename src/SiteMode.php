<?php
declare(strict_types=1);

namespace Trejjam\Configuration;

enum SiteMode : string
{
	case Default = 'Default';
	case Offline = 'Offline';
	case Local = 'Local';
	case Review = 'Review';
	case Staging = 'Staging';
	case Testing = 'Testing';
	case Public = 'Public';
}
