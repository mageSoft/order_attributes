<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace WebMeridian\OrderAttributes\Plugin\Magento\Sales\Api;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

class OrderRepositoryInterfacePlugin
{
    const FIELD_NAMES = ['test1', 'test2'];

    /**
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * OrderRepositoryInterfacePlugin constructor.
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @param int $id
     * @return mixed
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        /**@var OrderExtensionInterface $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes() ?: $this->extensionFactory->create();
        $this->setExtensionAttributesFromOrder($order, $extensionAttributes);
        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     * @return mixed
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $searchResult
    ) {
        $orders = $searchResult->getItems();
        foreach ($orders as &$order){
            /**@var OrderExtensionInterface $extensionAttributes */
            $extensionAttributes = $order->getExtensionAttributes() ?: $this->extensionFactory->create();
            $this->setExtensionAttributesFromOrder($order, $extensionAttributes);
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $searchResult;
    }

    /**
     * Get Extension attributes values from order & put it to OrderExtension obj
     * @param OrderInterface $order
     * @param OrderExtensionInterface $extensionAttributes
     */
    protected function setExtensionAttributesFromOrder($order, &$extensionAttributes){
        $orderData = $order->getData();
        foreach (self::FIELD_NAMES as $fieldName){
            $function = 'set'.ucfirst($fieldName);
            $extensionAttributes->$function($fieldName,$orderData[$fieldName]);
        }
    }

}

