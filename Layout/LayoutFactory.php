<?php

namespace Ekyna\Bundle\CmsBundle\Layout;

/**
 * LayoutFactory
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class LayoutFactory
{
    public function createLayout($id, $name, array $config)
    {
        return new Layout($id, $name, $this->fixConfiguration($config, $name));
    }

    private function fixConfiguration(array $config, $name)
    {
        $configuration = array();

        $rowWidth = 0;
        $row = 0;
        $column = 0;
        foreach ($config as $block) {
            $newRow = false;
            $blockWidth = $block['width'];
            if (0 == $rowWidth && $blockWidth >= 12) {
                $blockWidth = 12;
                $rowWidth = 0;                
            } elseif (0 < $diff = $rowWidth + $blockWidth - 12) {
                if ($diff > $blockWidth / 2) {
                    $configuration[count($configuration) - 1]['width'] = 12 - $rowWidth;
                } else {
                    $blockWidth -= $diff;
                }                
                $rowWidth = 0;
            } else {
                $rowWidth += $blockWidth;
                if($rowWidth >= 12) {
                    $rowWidth = 0;
                }
                $column++;
            }

            $block['row'] = $row;
            $block['column'] = $column;
            $block['width'] = $blockWidth;

            $configuration[] = $block;

            if(0 == $rowWidth) {
                $row++;
            }
        }

        if(0 != $rowWidth) {
            throw new \InvalidArgumentException(sprintf('Layout "%s"\'s blocks configuration is wrong.', $name));
        }

        return $configuration;

        /*foreach ($config as $row) {
            // Calcutates row width (blocks sum)
            $width = 0;
            array_walk($row, function($block, $key) use (&$width) {
            	$width += $block['width'];
            });

            // If row width not equals 12
            if (0 !== $diff = 12 - $width) {
                $nbBlocks = count($row);

                // If width is not enough
                if (0 < $diff) {
                    $diffByBlock = ceil($diff / $nbBlocks);
                    if ($diffByBlock < 1) {
                        $diffByBlock = 1;
                    }
                    $row = array_map($row, function($block) use (&$diff, &$diffByBlock) {
                        if ($diffByBlock > 0) {
                            if ($diff < $diffByBlock) {
                                $diffByBlock = $diff;
                                if ($diffByBlock < 0) {
                                    $diffByBlock = 0;
                                }
                            }
                            $block['width'] += $diffByBlock;
                            $diff -= $diffByBlock;
                        }
                        return $block;
                    });

                // If width is too much
                } else {
                    $diffByBlock = ceil(abs($diff) / $nbBlocks);
                    if ($diffByBlock < 1) {
                        $diffByBlock = 1;
                    }
                    $row = array_map($row, function($block) use (&$diff, &$diffByBlock) {
                        if ($diffByBlock > 0) {
                            if (abs($diff) < $diffByBlock) {
                                $diffByBlock = abs($diff);
                                if ($diffByBlock < 0) {
                                    $diffByBlock = 0;
                                }
                            }
                            $block['width'] -= $diffByBlock;
                            $diff += $diffByBlock;
                        }
                        return $block;
                    });
                }
            }

            $configuration[] = $row;
        }*/
    }
}
