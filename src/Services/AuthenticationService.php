<?php //strict

namespace IO\Services;

use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Modules\Account\Contact\Models\Contact;
use Plenty\Modules\Authentication\Contracts\ContactAuthenticationRepositoryContract;
use IO\Constants\SessionStorageKeys;

/**
 * Class AuthenticationService
 * @package IO\Services
 */
class AuthenticationService
{
	/**
	 * @var ContactAuthenticationRepositoryContract
	 */
	private $contactAuthRepository;
    
    /**
     * @var SessionStorageService $sessionStorage
     */
	private $sessionStorage;
    
    /**
     * AuthenticationService constructor.
     * @param ContactAuthenticationRepositoryContract $contactAuthRepository
     * @param \IO\Services\SessionStorageService $sessionStorage
     */
	public function __construct(ContactAuthenticationRepositoryContract $contactAuthRepository, SessionStorageService $sessionStorage)
	{
		$this->contactAuthRepository = $contactAuthRepository;
		$this->sessionStorage = $sessionStorage;
	}

    /**
     * Perform the login with email and password
     * @param string $email
     * @param string $password
     *
     * @return int
     */
	public function login(string $email, string $password)
	{
		$this->contactAuthRepository->authenticateWithContactEmail($email, $password);
		$this->sessionStorage->setSessionValue(SessionStorageKeys::GUEST_WISHLIST_MIGRATION, true);

        /** @var ContactRepositoryContract $contactRepository */
        $contactRepository = pluginApp(ContactRepositoryContract::class);

        return $contactRepository->getContactIdByEmail($email);
	}

    /**
     * Perform the login with customer ID and password
     * @param int $contactId
     * @param string $password
     */
	public function loginWithContactId(int $contactId, string $password)
	{
		$this->contactAuthRepository->authenticateWithContactId($contactId, $password);
        $this->sessionStorage->setSessionValue(SessionStorageKeys::GUEST_WISHLIST_MIGRATION, true);
	}

    /**
     * Log out the customer
     */
	public function logout()
	{
        $this->contactAuthRepository->logout();

        /**
         * @var BasketService $basketService
         */
        $basketService = pluginApp(BasketService::class);
        $basketService->setBillingAddressId(0);
        $basketService->setDeliveryAddressId(0);
	}

	public function checkPassword($password)
    {
        /** @var CustomerService $customerService */
        $customerService = pluginApp(CustomerService::class);
        $contact = $customerService->getContact();
        if ($contact instanceof Contact)
        {
            try
            {
                $this->login(
                    $contact->email,
                    $password
                );
                return true;
            }
            catch( \Exception $e )
            {
                return false;
            }
        }

        return false;
    }
}
