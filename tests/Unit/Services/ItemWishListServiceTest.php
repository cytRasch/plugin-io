<?php

use IO\Constants\SessionStorageKeys;
use IO\Repositories\ItemWishListGuestRepository;
use IO\Repositories\ItemWishListRepository;
use IO\Services\CustomerService;
use IO\Services\ItemWishListService;
use IO\Services\SessionStorageService;
use PluginTests\SimpleTestCase;

/**
 * User: lukasmatzen
 * Date: 02.10.18
 */
class ItemWishListServiceTest extends SimpleTestCase
{
    /** @var ItemWishListService $wishListService */
    protected $wishListService;

    protected $itemWishListRepositoryMock;
    protected $itemWishListGuestRepositoryMock;
    protected $customerServiceMock;
    protected $sessionStorageServiceMock;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->customerServiceMock = Mockery::mock(CustomerService::class);
        $this->replaceInstanceByMock(CustomerService::class, $this->customerServiceMock);

        $this->sessionStorageServiceMock = Mockery::mock(SessionStorageService::class);
        $this->sessionStorageServiceMock->shouldReceive('getSessionValue')->with(SessionStorageKeys::GUEST_WISHLIST_MIGRATION)->andReturnFalse();
        $this->replaceInstanceByMock(SessionStorageService::class, $this->sessionStorageServiceMock);
    }

    /** @test */
    public function it_checks_if_the_wish_list_repo_is_an_instance_of_item_wish_list_repository()
    {
        $this->itemWishListRepositoryMock = Mockery::mock(ItemWishListRepository::class);
        $this->itemWishListGuestRepositoryMock = Mockery::mock(ItemWishListGuestRepository::class);

        $this->itemWishListRepositoryMock->shouldReceive('getItemWishList')->andReturn(true);
        $this->itemWishListGuestRepositoryMock->shouldReceive('getItemWishList')->andReturn(false);

        $this->replaceInstanceByMock(ItemWishListRepository::class, $this->itemWishListRepositoryMock);
        $this->replaceInstanceByMock(ItemWishListGuestRepository::class, $this->itemWishListGuestRepositoryMock);


        $this->customerServiceMock->shouldReceive('getContactId')->once()->andReturn(1);

        $this->wishListService = pluginApp(ItemWishListService::class);

        $this->assertTrue($this->wishListService->getItemWishList());
    }

    /** @test */
    public function it_checks_if_the_wish_list_repo_is_an_instance_of_item_wish_list_guest_repository()
    {
        $this->itemWishListRepositoryMock = Mockery::mock(ItemWishListRepository::class);
        $this->itemWishListGuestRepositoryMock = Mockery::mock(ItemWishListGuestRepository::class);

        $this->replaceInstanceByMock(ItemWishListRepository::class, $this->itemWishListRepositoryMock);
        $this->replaceInstanceByMock(ItemWishListGuestRepository::class, $this->itemWishListGuestRepositoryMock);

        $this->itemWishListRepositoryMock->shouldReceive('getItemWishList')->andReturn(true);
        $this->itemWishListGuestRepositoryMock->shouldReceive('getItemWishList')->andReturn(false);

        $this->customerServiceMock->shouldReceive('getContactId')->once()->andReturn(0);

        $this->wishListService = pluginApp(ItemWishListService::class);

        $this->assertFalse($this->wishListService->getItemWishList());
    }
}
