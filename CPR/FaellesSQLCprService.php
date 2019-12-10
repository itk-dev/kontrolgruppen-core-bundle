<?php


namespace Kontrolgruppen\CoreBundle\CPR;

use Kontrolgruppen\CoreBundle\Entity\Client;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FaellesSQLCprService implements CprServiceInterface
{
    private CONST CITIZEN_ENDPOINT = 'citizen';

    private $serviceUrl;
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient, $serviceUrl)
    {
        $this->serviceUrl = $serviceUrl;
        $this->httpClient = $httpClient;
    }

    /**
     * @inheritDoc
     */
    public function find(int $cpr): array
    {
        $url = sprintf('%s/%s/%s', $this->serviceUrl,self::CITIZEN_ENDPOINT, $cpr);

        try {
            $response = $this->httpClient->request('GET', $url);
            return json_decode($response->getContent(), true);
        } catch (TransportExceptionInterface $e) {
            throw new CprException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function populateClient(int $cpr, Client $client): Client
    {
        $cprResult = $this->find($cpr);

        if (!empty($cprResult)) {

            $client->setFirstName($cprResult['Fornavn']);
            $client->setLastName($cprResult['Efternavn']);
            $client->setAddress($this->generateAddressString($cprResult));
            $client->setPostalCode($cprResult['Postnummer']);
            $client->setCity($cprResult['Bynavn']);
        }

        return $client;
    }

    private function generateAddressString($cprResult): string
    {
        $address  = $cprResult['Vejnavn'];
        $address .= ' ' . $cprResult['Husnr'];

        $address .= (!empty($cprResult['Etage'])) ? ' ' . $cprResult['Etage'] : '';
        $address .= (!empty($cprResult['Side'])) ? ' ' . $cprResult['Side'] : '';

        return $address;
    }

    /**
     * @inheritDoc
     */
    public function isNewClientInfoAvailable(int $cpr, Client $client): bool
    {
        $cprResult = $this->find($cpr);

        if (empty($cprResult)) {
            return false;
        }

        $comparisons = [
            $client->getFirstName() => $cprResult['Fornavn'],
            $client->getLastName() => $cprResult['Efternavn'],
            $client->getAddress() => $this->generateAddressString($cprResult),
            $client->getPostalCode() => $cprResult['Postnummer'],
            $client->getCity() => $cprResult['Bynavn'],
        ];

        foreach ($comparisons as $key => $value) {

            if (\strtolower(\trim($key)) !== \strtolower(\trim($value))) {
                return true;
            }
        }

        return false;
    }
}
