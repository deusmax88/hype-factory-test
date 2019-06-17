<?php

/**
 * Class BigFileReader
 * Реализация интерфейса SeekableIterator
 */
class BigFileReader implements SeekableIterator
{
    const READ_BYTES = 8192;

    protected $fd;

    protected $position = 0;

    public function __construct($fd)
    {
        $this->fd = $fd;
    }

    public function seek($position)
    {
        fseek($this->fd, $position);
    }

    public function current()
    {
        $readBytes = fread($this->fd, self::READ_BYTES);
        fseek($this->fd, ftell($this->fd) - self::READ_BYTES);

        return $readBytes;
    }

    public function next()
    {
        $this->position += self::READ_BYTES;
        fseek($this->fd, $this->position);
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
       $valid = fgetc($this->fd);
       fseek($this->fd, ftell($this->fd) - 1);

       return $valid;
    }

    public function rewind()
    {
        rewind($this->fd);
        $this->position = 0;
    }
}



$fd = fopen('test.txt', 'r');
$iterator = new BigFileReader($fd);

while($iterator->valid()) {
    echo $iterator->current();
    $iterator->next();
}
