<?php

/*
 * This file is part of the YtkoDoctrineBehaviors package.
 *
 * (c) Ytko <http://ytko.ru/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ytko\DoctrineBehaviors\Model;

trait Uploadable
{
    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    /**
     * @ORM\Column(name="upload_path", type="string", length=255, nullable=true)
     */
    private $uploadPath;

    private $previousUploadPath;

    public function setFile($file)
    {
        $this->file = $file;

        // keeping old file path fore later removing
        // shouldn't be changed many times, as the first one is from db
        $this->previousUploadPath = $this->previousUploadPath ?: $this->getUploadPath();

        // make Doctrine to understand that changes are made
        if (null !== $this->file) {
            // generate an unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->setUploadPath($filename.'.'.$this->file->guessExtension());
        } else {
            $this->setUploadPath(null);
        }

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setUploadPath($path)
    {
        $this->uploadPath = $path;
        return $this;
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function getAbsolutePath()
    {
        return null === $this->uploadPath
            ? null
            : $this->getUploadRootDir().'/'.$this->uploadPath;
    }

    public function getWebPath()
    {
        return null === $this->uploadPath
            ? null
            : $this->getUploadDir().'/'.$this->uploadPath;
    }

    private function getPreviousUploadAbsolutePath()
    {
        return null === $this->previousUploadPath
            ? null
            : $this->getUploadRootDir().'/'.$this->previousUploadPath;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->file->move(
            $this->getUploadRootDir(),
            $this->getUploadPath()
        );

        // remove the previous file
        $this->removeUpload(true);

        unset($this->file);
    }

    /**
     * Removes file from server
     *
     * @param bool $previous = false When true removes the file from $previousUploadPath (not $uploadPath) property
     */
    public function removeUpload($previous = false)
    {
        if ($file = $previous
                ? $this->getPreviousUploadAbsolutePath()
                : $this->getAbsolutePath()
        ) {
            try {
                unlink($file);
            } catch (\Exception $exception) {
                //TODO: send to logger
            }
        }
    }
}
