<?php


namespace App\Adapters;
use App\Interfaces\ParserInterface;
use App\Interfaces\ApiInterface;
use Exception;
use GuzzleHttp\Client;
use PhpParser\Node\Expr\Array_;

class PythonParserAdapter implements ParserInterface
{
    private $client;

    public function __construct() {
		//$this->configurations = config('app.megaindex');

//		$handler = new CurlHandler();
//		$stack = HandlerStack::create($handler); // Wrap w/ middleware


		$this->client = new Client([
			//'debug' => fopen('php://stderr', 'w'),
			//'handler' => $stack,
            'base_uri' => config('app.python.dev_url'),
            'headers' => ['content-type' => 'application/json']
        ]);
	}

    public function message()
    {
        return "Python parser adapter";
    }

    public function get()
    {
        // TODO: Implement getPosition() method.
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  string  $url
     * @param  string  $bodyJson
     * @return json
     */
    public function post($url, $bodyJson = '{}' )
    {
        try {
            $response = $this->client->request('POST', $url, ['body' => $bodyJson]);
            return json_decode( $response->getBody()->getContents() );

        } catch (\Exception $e) {
            throw new Exception();
        }
//        return json_decode($response->getBody()->getContents());
//        $request = $this->client->post(['body'=>[]]);
//        $response = $request->send();
    }
}
