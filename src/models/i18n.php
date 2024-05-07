<?php

// i18n sınıfı
class I18n
{
    private $pdo;
    private $redisConn;

    public function __construct($pdo, $redisConn)
    {
        $this->pdo = $pdo;
        $this->redisConn = $redisConn;
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $tableName = "i18n_words";
        $sql = "SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = :tableName)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tableName' => $tableName]);
        $tableExists = $stmt->fetchColumn();
        if (!$tableExists) {
            $this->createI18nWordsTable();
        }
    }

    private function createI18nWordsTable()
    {
        $sql = <<<SQL
            CREATE TABLE public.i18n_words (
                i18n_id SERIAL PRIMARY KEY,
                i18n_name VARCHAR(255) NOT NULL,
                i18n_value TEXT NOT NULL,
                i18n_language VARCHAR(255) DEFAULT 'tr',
                i18n_new INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );

            CREATE UNIQUE INDEX i18n_words_uniq1 ON public.i18n_words (i18n_name, i18n_language);
            
            CREATE OR REPLACE FUNCTION i18n_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                NEW.i18n_new=0;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trigger_i18n_words_updated_at
            BEFORE UPDATE ON public.i18n_words
            FOR EACH ROW
            EXECUTE FUNCTION i18n_updated_at();
SQL;

        $this->pdo->exec($sql);
    }

    public function getAllLanguages()
    {
        $storedLanguageData = $this->redisConn->get('i18n_languages');
        $storedLanguageData = json_decode($storedLanguageData, true);

        $query = "SELECT DISTINCT i18n_language as language FROM i18n_words";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getExistingWords()
    {
        return $this->getWordsByNewStatus(0);
    }

    public function getNewWords()
    {
        return $this->getWordsByNewStatus(1);
    }

    private function getWordsByNewStatus($newStatus)
    {
        $storedLanguageData = $this->redisConn->get('i18n_words_' . (string) $newStatus);
        $storedLanguageData = json_decode($storedLanguageData, true);
        return $storedLanguageData;
    }

    public function _t($name, $language = 'tr')
    {
        $storedLanguageData = $this->redisConn->get('i18n_words');
        $storedLanguageData = json_decode($storedLanguageData, true);

        $sql = "SELECT i18n_value FROM i18n_words WHERE i18n_name = :name AND i18n_language = :language";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'language' => $language]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $this->createTranslation($name, $language);
            return $name;
        }

        return $result['i18n_value'];
    }

    private function createTranslation($name, $language)
    {
        $sql = "INSERT INTO i18n_words (i18n_name, i18n_language, i18n_value, i18n_new) VALUES (:name, :language, :value, 1)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['name' => $name, 'language' => $language, 'value' => $name]);
        $this->updateRedis();
    }

    public function addTranslation($name, $value, $language)
    {
        try {
            $sql = "INSERT INTO i18n_words (i18n_name, i18n_value, i18n_language) VALUES (:name, :value, :language)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['name' => $name, 'value' => $value, 'language' => $language]);
            $this->updateRedis();
            return true;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                throw new Exception("duplicate");
            } else {
                throw new Exception("Database error: " . $e->getMessage());
            }
        }
    }

    public function updateTranslation($id, $value)
    {
        try {
            $sql = "UPDATE i18n_words SET i18n_value = :value WHERE i18n_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id, 'value' => $value]);
            $this->updateRedis();
            return true;
        } catch (Exception $e) {
            throw new Exception('' . $e->getMessage());
        }
    }

    public function deleteTranslation($id)
    {
        $sql = "DELETE FROM i18n_words WHERE i18n_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $this->updateRedis();
        return true;
    }

    public function updateRedis()
    {
        $query = "SELECT i18n_id as id, i18n_name as block, i18n_value as value, i18n_language as language 
                  FROM i18n_words 
                  WHERE i18n_new = :newStatus 
                  ORDER BY updated_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['newStatus' => 1]);
        $storedData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->redisConn->set('i18n_words_1', json_encode($storedData));

        $query = "SELECT i18n_id as id, i18n_name as block, i18n_value as value, i18n_language as language 
                  FROM i18n_words 
                  WHERE i18n_new = :newStatus 
                  ORDER BY updated_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['newStatus' => 0]);
        $storedData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->redisConn->set('i18n_words_0', json_encode($storedData));
    }
}
?>