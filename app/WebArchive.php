<?php class WebArchive {

    public function save($url) {

        $url = 'https://web.archive.org/save/' . $url;
        $data = $this->getUrl($url);

        if (!array_key_exists('content-location', $data))
            throw new \Exception('Error occured. Page saving failed.');

        return 'https://web.archive.org' . $data['content-location'];
    }

    protected function getUrl($url) {

        $prepare = curl_init();

        curl_setopt($prepare, CURLOPT_URL, $url);
        curl_setopt($prepare, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($prepare, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($prepare, CURLOPT_HEADER, true);
        curl_setopt($prepare, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($prepare, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($prepare, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($prepare, CURLOPT_TIMEOUT, 60);
        curl_setopt($prepare, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.94 Safari/537.36');

        $send = curl_exec($prepare);

        if ($send) {
            curl_close($prepare);
            return $this->getHeaders($send);
        } else {
            throw new \Exception(curl_error($prepare));
        }
    }

    private function getHeaders($response) {
        $headers = array();
        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0)
                $headers['http_code'] = $line;
            else {
                list ($key, $value) = explode(': ', $line);
                $headers[strtolower($key)] = $value;
            }
        }
        return $headers;
    }
}
