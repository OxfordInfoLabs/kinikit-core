<?php


namespace Kinikit\Core\Stream\FTP;


use Kinikit\Core\Stream\Resource\ReadOnlyFilePointerResourceStream;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SFTP\Stream;

/**
 * Read only FTP stream
 *
 * Class ReadOnlyFTPStream
 * @package Kinikit\Core\Stream\FTP
 */
class ReadOnlyFTPStream extends ReadOnlyFilePointerResourceStream {


    /**
     * ReadOnlyFTPStream constructor.
     *
     * @param $hostname
     * @param $filePath
     * @param bool $secure
     * @param string $username
     * @param string $password
     * @param string $privateKey
     */
    public function __construct($hostname, $filePath, $secure = true, $username = null, $password = null, $privateKey = null) {

        // Secure case
        if ($secure) {

            Stream::register("sftp");

            $sftpContext = [];
            if ($username) $sftpContext["username"] = $username;
            if ($password) $sftpContext["password"] = $password;
            if ($privateKey) $sftpContext["privkey"] = PublicKeyLoader::load(file_get_contents($privateKey));

            $context = [
                "sftp" => $sftpContext
            ];

            $context = stream_context_create($context);

            parent::__construct(fopen("sftp://$hostname/$filePath", "r", false, $context));

        } else // Insecure case
        {

            $ftp = ftp_connect($hostname);
            if ($username) {
                $login = ftp_login($ftp, $username, $password);
                if ($login) {
                    $outputHandle = fopen("php://temp", "r+");

                    ftp_pasv($ftp, true);
                    ftp_fget($ftp, $outputHandle, $filePath,  FTP_ASCII);

                    parent::__construct($outputHandle);
                }
            }


        }

    }

}