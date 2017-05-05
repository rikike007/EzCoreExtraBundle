<?php

/*
 * This file is part of the EzCoreExtraBundle package.
 *
 * (c) Jérôme Vieilledent <jerome@vieilledent.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lolautruche\EzCoreExtraBundle\Asset;

use Lolautruche\EzCoreExtraBundle\Exception\InvalidDesignException;
use Psr\Log\LoggerInterface;

class AssetPathResolver implements AssetPathResolverInterface
{
    /**
     * @var array
     */
    private $designPaths;

    /**
     * @var string
     */
    private $webRootDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(array $designPaths, $webRootDir, LoggerInterface $logger = null)
    {
        $this->designPaths = $designPaths;
        $this->webRootDir = $webRootDir;
        $this->logger = $logger;
    }

    public function resolveAssetPath($path, $design)
    {
        if (!isset($this->designPaths[$design])) {
            throw new InvalidDesignException("Invalid design '$design'");
        }

        foreach ($this->designPaths[$design] as $tryPath) {
            if (file_exists($this->webRootDir.'/'.$tryPath.'/'.$path)) {
                return $tryPath.'/'.$path;
            }
        }

        if ($this->logger) {
            $this->logger->warning(
                "Asset '$path' cannot be found in any configured themes.\nTried directories: ".implode(
                    ', ',
                    array_values($this->designPaths[$design])
                )
            );
        }

        return $path;
    }
}
