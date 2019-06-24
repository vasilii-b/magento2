<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Magento\SendFriendGraphQl\Model\Provider;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GetProduct
 */
class GetProduct
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var Visibility */
    private $visibility;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Visibility $visibility
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Visibility $visibility
    ) {
        $this->productRepository = $productRepository;
        $this->visibility = $visibility;
    }

    /**
     * Get product
     *
     * @param int $productId
     * @return ProductInterface
     * @throws GraphQlNoSuchEntityException
     */
    public function execute(int $productId): ProductInterface
    {
        try {
            $product = $this->productRepository->getById($productId);

            if (!in_array(
                $product->getVisibility(),
                $this->visibility->getVisibleInCatalogIds()
            )) {
                throw new GraphQlNoSuchEntityException(
                    __("The product that was requested doesn't exist. Verify the product and try again.")
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $product;
    }
}
