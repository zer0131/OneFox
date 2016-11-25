<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc curl请求封装
 */

namespace onefox;

class Curl extends Base {
    private $_ch;
    private $_config = array(
        CURLOPT_HEADER => false, // 不显示header信息
        CURLOPT_RETURNTRANSFER => true, // 将获取的信息以文件流的形式返回，而不是直接输出。
        CURLOPT_FOLLOWLOCATION => true, // 使用自动跳转
        CURLOPT_AUTOREFERER => true, // 自动设置Referer
        CURLOPT_SSL_VERIFYPEER => false, // 对认证证书来源的检查
        CURLOPT_SSL_VERIFYHOST => false, // 从证书中检查SSL加密算法是否存在
        CURLOPT_TIMEOUT => 10,// 执行时间
        CURLOPT_CONNECTTIMEOUT =>3,//连接时间
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:11.0) Gecko/20100101 Firefox/11.0',//请求UA
    );

    public function __construct() {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('cuel扩展未加载');
        }
        $this->_ch = curl_init();
    }

    /**
     * GET请求
     * 请求时url中不需要携带参数，参数以$params数组传入
     */
    public function get($url, $params = array(), $header = array()) {
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        $header && $this->_config[CURLOPT_HTTPHEADER] = $header;
        return $this->request($url);
    }

    /**
     * POST请求
     * $multi为数组时，则可以传文件
     */
    public function post($url, $params = array(), $header = array(), $isJson = false, $multi = false) {
        $this->_config[CURLOPT_URL] = $url;
        $this->_config[CURLOPT_POST] = true;
        if ($params) {
            if ($multi && is_array($multi)) {
                foreach ($multi as $key => $file) {
                    if (version_compare(PHP_VERSION, '5.5.0', '<')) {
                        $params[$key] = '@' . $file;
                    } else {
                        $params[$key] = curl_file_create($file);//php5.5以后使用这种方式创建file
                    }
                }
            } else {
                $this->_config[CURLOPT_POSTFIELDS] = $isJson ? json_encode($params) : http_build_query($params);
            }
        }
        $header && $this->_config[CURLOPT_HTTPHEADER] = $header;
        curl_setopt_array($this->_ch, $this->_config);
        $result = curl_exec($this->_ch);
        return $result;
    }

    /**
     * PUT请求
     */
    public function put($url, $params = array(), $header = array(), $isJson = false) {
        $this->_config[CURLOPT_CUSTOMREQUEST] = 'PUT';
        if ($params) {
            $this->_config[CURLOPT_POSTFIELDS] = $isJson ? json_encode($params) : http_build_query($params);
        }
        $header && $this->_config[CURLOPT_HTTPHEADER] = $header;
        return $this->request($url);
    }

    /**
     * DELETE请求
     */
    public function delete($url, $params = array(), $header = array(), $isJson = false) {
        $this->_config[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        if ($params) {
            $this->_config[CURLOPT_POSTFIELDS] = $isJson ? json_encode($params) : http_build_query($params);
        }
        $header && $this->_config[CURLOPT_HTTPHEADER] = $header;
        return $this->request($url);
    }

    /**
     * curl高级请求，可以根据自己的需要设置curl
     */
    public function request($url, $curlOpt = array()) {
        $this->_config[CURLOPT_URL] = $url;
        if ($curlOpt && is_array($curlOpt)) {
            foreach ($curlOpt as $key => $val) {
                $this->_config[$key] = $val;
            }
        }
        curl_setopt_array($this->_ch, $this->_config);
        $result = curl_exec($this->_ch);
        return $result;

    }

    /**
     * 请求详细信息
     */
    public function getInfo($opt = 0) {
        $info = curl_getinfo($this->_ch, $opt);
        return $info;
    }

    /**
     * 错误信息
     */
    public function getError() {
        return curl_error($this->_ch);
    }

    /**
     * 错误号，为0则无错误
     */
    public function isError() {
        return curl_errno($this->_ch);
    }

    public function close() {
        curl_close($this->_ch);
        if ($this->_ch) {
            $this->_ch = null;
        }
    }

    public function __destruct() {
        $this->close();
    }
}
