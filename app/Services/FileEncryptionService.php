<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class FileEncryptionService
{
    public function encryptAndStore($file, $path)
    {
        $content = file_get_contents($file->getRealPath());
        $encryptedContent = Crypt::encryptString($content);
        
        $encryptedPath = 'encrypted/' . $path;
        Storage::put($encryptedPath, $encryptedContent);
        
        return $encryptedPath;
    }
    
    public function storeInvoice($file, $expenseId)
    {
        $filename = 'invoices/' . $expenseId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $this->encryptAndStore($file, $filename);
    }
    
    public function decryptFile($encryptedPath)
    {
        if (!Storage::exists($encryptedPath)) {
            throw new \Exception('File not found');
        }
        
        $encryptedContent = Storage::get($encryptedPath);
        return Crypt::decryptString($encryptedContent);
    }
    
    public function deleteEncryptedFile($encryptedPath)
    {
        if (Storage::exists($encryptedPath)) {
            Storage::delete($encryptedPath);
            return true;
        }
        return false;
    }
    
    public function serveEncryptedFile($encryptedPath, $originalName = null)
    {
        $decryptedContent = $this->decryptFile($encryptedPath);
        
        $mimeType = $this->getMimeType($originalName);
        
        return response($decryptedContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . ($originalName ?? 'file') . '"');
    }
    
    private function getMimeType($filename)
    {
        if (!$filename) return 'application/octet-stream';
        
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}