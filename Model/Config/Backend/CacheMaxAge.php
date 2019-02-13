<?php

namespace Resultate\PWA\Model\Config\Backend;

class CacheMaxAge implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('1 Dia')],
            ['value' => 2, 'label' => __('2 Dias')],
            ['value' => 3, 'label' => __('3 Dias')],
            ['value' => 4, 'label' => __('4 Dias')],
            ['value' => 5, 'label' => __('5 Dias')],
            ['value' => 6, 'label' => __('6 Dias')],
            ['value' => 7, 'label' => __('7 Dias')],
        ];
    }
}