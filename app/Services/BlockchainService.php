<?php

namespace App\Services;

class BlockchainService
{
    private $chain = [];
    private $difficulty = 4;

    public function __construct()
    {
        $this->createGenesisBlock();
    }

    public function addExpenseToBlockchain($expenseData)
    {
        $previousBlock = $this->getLatestBlock();
        $newBlock = [
            'index' => count($this->chain),
            'timestamp' => time(),
            'data' => $expenseData,
            'previous_hash' => $previousBlock['hash'],
            'nonce' => 0
        ];

        $newBlock['hash'] = $this->mineBlock($newBlock);
        $this->chain[] = $newBlock;

        // حفظ في قاعدة البيانات
        \App\Models\BlockchainRecord::create([
            'block_index' => $newBlock['index'],
            'block_hash' => $newBlock['hash'],
            'previous_hash' => $newBlock['previous_hash'],
            'data' => json_encode($newBlock['data']),
            'timestamp' => $newBlock['timestamp'],
            'nonce' => $newBlock['nonce']
        ]);

        return $newBlock;
    }

    public function verifyChainIntegrity()
    {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];

            // التحقق من صحة الهاش
            if ($currentBlock['hash'] !== $this->calculateHash($currentBlock)) {
                return ['valid' => false, 'error' => "Invalid hash at block {$i}"];
            }

            // التحقق من الربط مع البلوك السابق
            if ($currentBlock['previous_hash'] !== $previousBlock['hash']) {
                return ['valid' => false, 'error' => "Invalid link at block {$i}"];
            }
        }

        return ['valid' => true, 'message' => 'Blockchain is valid'];
    }

    public function getExpenseHistory($expenseId)
    {
        $history = [];
        
        foreach ($this->chain as $block) {
            if (isset($block['data']['expense_id']) && $block['data']['expense_id'] == $expenseId) {
                $history[] = [
                    'block_index' => $block['index'],
                    'timestamp' => $block['timestamp'],
                    'action' => $block['data']['action'],
                    'user' => $block['data']['user_id'],
                    'hash' => $block['hash']
                ];
            }
        }

        return $history;
    }

    public function createAuditTrail($action, $entityType, $entityId, $userId, $changes = [])
    {
        $auditData = [
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'user_id' => $userId,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        return $this->addExpenseToBlockchain($auditData);
    }

    private function createGenesisBlock()
    {
        $genesisBlock = [
            'index' => 0,
            'timestamp' => time(),
            'data' => ['message' => 'Genesis Block - PEMS Blockchain'],
            'previous_hash' => '0',
            'nonce' => 0
        ];

        $genesisBlock['hash'] = $this->calculateHash($genesisBlock);
        $this->chain[] = $genesisBlock;
    }

    private function getLatestBlock()
    {
        return end($this->chain);
    }

    private function calculateHash($block)
    {
        return hash('sha256', 
            $block['index'] . 
            $block['timestamp'] . 
            json_encode($block['data']) . 
            $block['previous_hash'] . 
            $block['nonce']
        );
    }

    private function mineBlock($block)
    {
        $target = str_repeat('0', $this->difficulty);
        
        while (substr($this->calculateHash($block), 0, $this->difficulty) !== $target) {
            $block['nonce']++;
        }

        return $this->calculateHash($block);
    }

    public function loadChainFromDatabase()
    {
        $records = \App\Models\BlockchainRecord::orderBy('block_index')->get();
        
        $this->chain = [];
        foreach ($records as $record) {
            $this->chain[] = [
                'index' => $record->block_index,
                'timestamp' => $record->timestamp,
                'data' => json_decode($record->data, true),
                'previous_hash' => $record->previous_hash,
                'hash' => $record->block_hash,
                'nonce' => $record->nonce
            ];
        }
    }

    public function getBlockchainStats()
    {
        return [
            'total_blocks' => count($this->chain),
            'latest_block_hash' => $this->getLatestBlock()['hash'],
            'chain_valid' => $this->verifyChainIntegrity()['valid'],
            'difficulty' => $this->difficulty
        ];
    }
}