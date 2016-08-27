<?php
namespace Woozapp\Form\Handlers;

use Woozapp\Plugin;
use Woozapp\Form\Exceptions\ExceptionUpload;

/**
 * File Handler
 *
 * @package Woozapp\Form\Handlers
 *
 * Copyright (C) 2016 Guido Scialfa <dev@guidoscialfa.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined('WPINC') || die;

/**
 * Class Handle Files
 *
 * @since  1.0.0
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
final class Files
{
    /**
     * Path
     *
     * @since  1.0.0
     * @access private
     *
     * @var string Where store the file
     */
    private $path;

    /**
     * Allowed Mime Type
     *
     * @since  1.0.0
     * @access private
     *
     * @var mixed The allowed mime type for the current file.
     */
    private $allowedMime;

    /**
     * Max File Size
     *
     * @since  1.0.0
     * @access private
     *
     * @var int The max file size.
     */
    private $maxSize;

    /**
     * Image Resolution
     *
     * @since  1.0.0
     * @access private
     *
     * @var array The width and height of the image
     */
    private $resolution;

    /**
     * Mime Types List Path
     *
     * @since  1.0.0
     * @access private
     *
     * @var array The path of the mime types json
     */
    private $mtPath;

    /**
     * WP FileSystem
     *
     * @since  1.0.0
     * @access private
     *
     * @var \WP_Filesystem_Direct Instance
     */
    private $filesystem;

    /**
     * Retrieve the list of mime types
     *
     * @todo   Need to integrate the mimetype json.
     *
     * @since  1.0.0
     * @access private
     *
     * @return array A list of mime and their extension
     */
    private function getMimeTypes()
    {
        // Decode the json file.
        $json = json_decode($this->filesystem->get_contents($this->mtPath));

        if (! $json) {
            return array();
        }

        return (array)$json;
    }

    /**
     * Get the correct file extension from the mime file
     *
     * @since  1.0.0
     * @access private
     *
     * @throws \Exception If the mime type provided is not correct.
     *
     * @param string $mime The mime type of the file.
     *
     * @return string The file extension, empty string if the extension cannot be retrieved.
     */
    private function getExtFromMime($mime)
    {
        // Not a valid mime.
        if (false === strpos($mime, '/')) {
            return '';
        }

        // Get the mime types.
        $exts = array_flip($this->getMimeTypes());

        if (empty($exts)) {
            return '';
        }

        return (isset($exts[$mime]) ? $exts[$mime] : '');
    }

    /**
     * Handle the file
     *
     * Handle the file and move into appropiated directory.
     *
     * @since  1.0.0
     * @access private
     *
     * @throws ExceptionUpload If the handler encountered some issue.
     * @throws \Exception              If the file cannot be moved.
     *
     * @param array  $file  The file data to process. Array similar to a $_FILES upload array.
     * @param string $fname The new filename.
     *
     * @return bool|string False if the file parameter is not an array or the path is empty. The file path on success.
     */
    public function uploadFile(array $file = array(), $fname = '')
    {
        if (empty($file)) {
            throw new ExceptionUpload(UPLOAD_ERR_CANT_WRITE);
        }

        if (! empty($file['error'])) {
            throw new ExceptionUpload($file['error']);
        } elseif ($file['size'] > $this->maxSize) {
            throw new ExceptionUpload(UPLOAD_ERR_FORM_SIZE);
        } elseif (! in_array($file['type'], $this->allowedMime, true)) {
            throw new ExceptionUpload(UPLOAD_ERR_EXTENSION);
        }

        /*if (0 !== $this->resolution) {
            // @todo Need test about the throw with different file format.
            list($width, $height) = getimagesize($file['tmp_name']);

            // Check for the image resolution.
            // @todo Add compare min, max, equal.
            if ($width !== $this->resolution[0] || $height !== $this->resolution[1]) {
                throw new ExceptionUpload(ExceptionUpload::UPLOAD_ERR_IMG_SIZE);
            }
        }*/

        /*
         * File Name
         *
         * Set the file name by trying the one passed to the method or get the one
         * from the file array.
         */
        $ext         = $this->getExtFromMime($file['type']);
        $fname       = ! empty($fname) ? "{$fname}.{$ext}" : $file['name'];
        $filename    = untrailingslashit($this->path) . '/' . sanitize_file_name($fname);
        $fileContent = $this->filesystem->get_contents($file['tmp_name']);

        // File is Empty?
        if (false === $fileContent) {
            throw new ExceptionUpload(UPLOAD_ERR_CANT_WRITE);
        }

        // Create the directory if not exists.
        if (! $this->filesystem->exists($this->path)) {
            // Create the directories recursively.
            if (false === wp_mkdir_p($this->path)) {
                throw new ExceptionUpload(UPLOAD_ERR_CANT_WRITE);
            }
        }

        // The directory is not writable? Ops!
        if (! $this->filesystem->is_writable($this->path)) {
            throw new ExceptionUpload(UPLOAD_ERR_CANT_WRITE);
        }

        // If file exists most probably we are editing something, let's overwrite the file.
        // @todo Add parameter to prevent delete. Instead add a number. Use wp_unique_filename.
        if ($this->filesystem->exists($filename)) {
            $this->filesystem->delete($filename);
        }

        if (! $this->filesystem->put_contents($filename, $fileContent, FS_CHMOD_FILE)) {
            throw new ExceptionUpload(UPLOAD_ERR_CANT_WRITE);
        }

        return $filename;
    }

    /**
     * Remove File
     *
     * @since  1.0.0
     * @access private
     *
     * @throws \Exception if file doesn't exists.
     *
     * @param string $file The filename to remove.
     *
     * @return bool True on success false on failure
     */
    public function removeFile($file)
    {
        // Build the file path.
        $filePath = untrailingslashit($this->path) . "/{$file}";

        if (! $this->filesystem->exists($filePath)) {
            // @todo Build a custom Exception Class.
            throw new \Exception(esc_html__('File doesn\'t exists', 'woozapp'));
        }

        if (! $this->filesystem->delete($filePath)) {
            throw new \Exception(esc_html__('Can\'t delete the file.', 'woozapp'));
        }
    }

    /**
     * Handle Dirs
     *
     * The method 'delete' remove a directory recursively.
     *
     * @todo   Add more comprensive exception message.
     * @todo   Move to HandleDirs
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \Exception If the object path is not found in directory parameter.
     *
     * @param string $action The method of WP_Filesystem_Direct instance to call.
     * @param string $dir    The directory to remove.
     *
     * @return bool If is not a dir
     */
    public function handleDir($action, $dir)
    {
        // Prevent to remove directory that are not under the current object path.
        if (false === strpos($dir, $this->path)) {
            throw new \Exception(esc_html__('You are trying to remove a directory that is not allowed.', 'woozapp'));
        }

        // Not a dir?
        if (! $this->filesystem->is_dir($dir)) {
            return false;
        }

        return $this->filesystem->{$action}($dir, true);
    }

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     *
     * @throws ExceptionUpload If the file array is empty or path is empty.
     *
     * @param string $path       Where store the file.
     * @param array  $mimeTypes  A list of allowed mime types. Optional.
     * @param int    $maxSize    The value of the max upload size allowed. Optional. Default to wp_max_upload_size().
     * @param array  $resolution The resolution of the image.
     */
    public function __construct($path, array $mimeTypes = array(), $maxSize = 0, $resolution = array())
    {
        if ('' === $path) {
            throw new ExceptionUpload(UPLOAD_ERR_CANT_WRITE);
        }

        // Set the permission constants if not already set.
        if (! defined('FS_CHMOD_DIR')) {
            define('FS_CHMOD_DIR', (fileperms(ABSPATH) & 0777 | 0755));
        }
        if (! defined('FS_CHMOD_FILE')) {
            define('FS_CHMOD_FILE', (fileperms(ABSPATH . 'index.php') & 0777 | 0644));
        }

        if (! class_exists('WP_Filesystem_Direct')) {
            require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/class-wp-filesystem-base.php';
            require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/class-wp-filesystem-direct.php';
        }

        $uploadDir = wp_upload_dir();

        // Don't append the upload base dir twice.
        if (false !== strpos($path, $uploadDir['basedir'])) {
            $this->path = $path;
        } else {
            $this->path = untrailingslashit($uploadDir['basedir']) . '/' . ltrim($path, '/');
        }

        $this->filesystem = new \WP_Filesystem_Direct(array());
        $this->maxSize    = (! $maxSize ? wp_max_upload_size() : $maxSize);
        // @todo Need a test with different file formats.
        // @todo Every element must be validated as int?
        //$this->resolution  = ($resolution ? $resolution : array(0, 0));
        $this->allowedMime = $mimeTypes;
        $this->mtPath      = Plugin::getPluginDirPath() . '/assets/json/mimeTypes.json';
    }

    /**
     * Is Empty Dir
     *
     * @todo  Move to HandleDirs
     *
     * @since 1.0.0
     *
     * @param string $dir The directory to test
     *
     * @return bool True if empty, false if not a dir or not empty.
     */
    public function isEmptyDir($dir = '')
    {
        if ('' === $dir) {
            $dir = $this->path;
        }

        if (! $this->filesystem->is_dir($dir)) {
            return false;
        }

        return ! count($this->filesystem->dirlist($dir));
    }
}
