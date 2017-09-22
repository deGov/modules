<?php

namespace Drupal\degov_common\Controller;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\FileInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileController
 */
class FileController extends ControllerBase {

  /**
   * Returns a HTTP response for a file being downloaded.
   *
   * @param FileInterface $file
   *   The file to download, as an entity.
   *
   * @return Response
   *   The file to download, as a response.
   */
  public function download(FileInterface $file) {
    // Get correct headers.
    $headers = array(
      'Content-Type' => Unicode::mimeHeaderEncode($file->getMimeType()),
      'Content-Disposition' => 'attachment; filename="' . Unicode::mimeHeaderEncode(drupal_basename($file->getFileUri())) . '"',
      'Content-Length' => $file->getSize(),
      'Content-Transfer-Encoding' => 'binary',
      'Pragma' => 'no-cache',
      'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
      'Expires' => '0',
    );

    // Let other modules alter the download headers.
    \Drupal::moduleHandler()->alter('file_download_headers', $headers, $file);

    // Let other modules know the file is being downloaded.
    \Drupal::moduleHandler()->invokeAll('file_transfer', array($file->getFileUri(), $headers));

    try {
      return new BinaryFileResponse($file->getFileUri(), 200, $headers);
    }
    catch (FileNotFoundException $e) {
      return new Response(t('File @uri not found', array('@uri' =>$file->getFileUri())), 404);
    }
  }

}
