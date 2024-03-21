<?php


namespace Kinikit\Core\Stream\FTP;


use Kinikit\Core\Exception\AccessDeniedException;
use Kinikit\Core\Exception\FileNotFoundException;
use Kinikit\Core\Stream\Resource\ReadOnlyFilePointerResourceStream;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SFTP;

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
     * @param string $privateKey An ssh key as text
     */
    public function __construct($hostname, $filePath, $secure = true, $username = null, $password = null, $privateKey = null) {

        // Secure case
        if ($secure) {

            $sftp = new SFTP($hostname);
            $authenticated = $sftp->login($username, $privateKey ? PublicKeyLoader::load($privateKey) : $password);

            if (!$authenticated) {
                throw new AccessDeniedException("Could not connect to FTP server with supplied credentials");
            }

            $tmpDir = sys_get_temp_dir();
            $download = tempnam($tmpDir, "ftp");
            $downloaded = $sftp->get($filePath, $download);
            if (!$downloaded) {
                throw new FileNotFoundException("The file at path $filePath cannot be found on the FTP server");
            }
            parent::__construct(fopen($download, "r"));

        } else // Insecure case
        {

            $ftp = ftp_connect($hostname);
            if ($username) {
                $login = ftp_login($ftp, $username, $password);
                if ($login) {
                    $outputHandle = fopen("php://temp", "r+");

                    ftp_pasv($ftp, true);
                    ftp_fget($ftp, $outputHandle, $filePath, FTP_ASCII);

                    parent::__construct($outputHandle);
                }
            }


        }

    }

}