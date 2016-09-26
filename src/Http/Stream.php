<?php
/**
 * This file is part of Student-List application.
 *
 * @author foobar1643 <foobar76239@gmail.com>
 * @copyright 2016 foobar1643
 * @package Students\Http
 * @license https://github.com/foobar1643/student-list/blob/master/LICENSE.md MIT License
 */

namespace Students\Http;

use Psr\Http\Message\StreamInterface;

/**
 * Represents a data stream according to PSR-7.
 *
 * @todo Process file pointers support?
 *
 * @link http://www.php-fig.org/psr/psr-7/#3-4-psr-http-message-streaminterface
 */
class Stream implements StreamInterface
{
    /**
     * Stream resource.
     * @var resource
     */
    protected $stream;

    /**
     * Stream metadata.y
     * @var array
     */
    protected $metadata;

    /**
     * Array of readable modes for streams.
     * @var string[]
     */
    protected $readableModes = ['r', 'r+', 'w+', 'a+', 'x', 'c+'];

    /**
     * Array of writeable modes for streams.
     * @var string[]
     */
    protected $writableModes = ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'];

    /**
     * Constructor.
     *
     * @param resource $stream Stream resource.
     */
    public function __construct($stream)
    {
        $this->attach($stream);
    }

    /**
     * Attaches given stream.
     *
     * @throws \InvalidArgumentException If given stream is not a PHP resource.
     *
     * @return void
     */
    protected function attach($stream)
    {
        if(!is_resource($stream)) {
            throw new \InvalidArgumentException("Stream must be a resource.");
        }
        $this->stream = $stream;
        $this->metadata = stream_get_meta_data($stream);
    }

    /**
     * Returns whether or not stream is attached.
     *
     * @return bool
     */
    protected function isAttached()
    {
        return !is_null($this->stream);
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @todo Check if strpos would be safe to use here.
     *
     * @return bool
     */
    public function isReadable()
    {
        if($this->isAttached()) {
            foreach($this->readableModes as $key => $mode) {
                $pattern = "/^" . preg_quote($mode) . "{1,2}/";
                if(preg_match($pattern, $this->metadata['mode'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        if($this->isAttached()) {
            foreach($this->writableModes as $key => $mode) {
                $pattern = "/^" . preg_quote($mode) . "{1,2}/";
                if(preg_match($pattern, $this->metadata['mode'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return ($this->isAttached()) ? $this->metadata['seekable'] : false;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if(!$this->isAttached() || (($data = fread($this->stream, $length)) === false)) {
            throw new \RuntimeException("An error occured while reading the stream.");
        }
        return $data;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if(!$this->isAttached() || (($bytes = fwrite($this->stream, $string)) === false)) {
            throw new \RuntimeException("An error occured while writing to the stream.");
        }
        return $bytes;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     *
     * @return void
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if(!$this->isAttached() || (fseek($this->stream, $offset, $whence) === -1)) {
            throw new \RuntimeException("An error occured while seeking to a position in the stream.");
        }
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        $contents = stream_get_contents($this->stream);
        if(!$this->isAttached() || $contents === false) {
            throw new \RuntimeException("An error occured while reading the contents of the stream.");
        }
        return $contents;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if(is_null($key)) {
            return $this->metadata;
        }
        return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        $stat = fstat($this->stream);
        if($this->isAttached() && isset($stat['size'])) {
            return $stat['size'];
        }
        return null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        $position = ftell($this->stream);
        if(!$this->isAttached() || $position === false) {
            throw new \RuntimeException("An error occured while getting pointer position.");
        }
        return $position;
    }

    /**
     * Returns true if the pointer is at the end of the stream.
     *
     * @throws \RuntimeException If an error occured.
     *
     * @return bool
     */
    public function eof()
    {
        if(!$this->isAttached()) {
            throw new \RuntimeException("Stream is not attached, can't check if the pointer is at the end of the stream.");
        }
        return feof($this->stream);
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        if(!$this->isSeekable() || (fseek($this->stream, 0)) === -1) {
            throw new \RuntimeException("An error occured while rewinding the stream.");
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $stream = $this->stream;
        $this->metadata = null;
        $this->stream = null;
        return $stream;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if($this->isAttached()) {
            fclose($this->stream);
            $this->detach();
        }
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        $data = '';
        try {
            $this->rewind();
            $data = $this->getContents();
        } catch(\RuntimeException $e) {
            return '';
        }
        return $data;
    }
}