<?php
  /**
   * API RESPONSE STANDAR BY PUDIN
   */
  protected function successResponse($data = null, $message = 'Success', $code = 200)
  {
      return $this->response->setJSON([
          'status' => true,
          'code' => $code,
          'message' => $message,
          'data' => $data,
          'errors' => null
      ])->setStatusCode($code);
  }

  protected function errorResponse($message = 'Error', $errors = null, $code = 400)
  {
      return $this->response->setJSON([
          'status' => false,
          'code' => $code,
          'message' => $message,
          'data' => null,
          'errors' => $errors
      ])->setStatusCode($code);
  }
