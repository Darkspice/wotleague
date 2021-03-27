<?php 
include_once 'config.php';
//Если не залогинен - переадресовываем
if( !isset($_SESSION['status']) ) {
    header('Location: '.AUTHURL);
}
//Проверка на то, что это уведомление от Яндекса
if(isset($_POST['notification_type'])) {

    $secret_key = 'ZwAlbhSADFGsasFDeY2fMF+VMKG+Wa'; // секретное слово, которое мы получили в настройках http-уведомлений на сайте Яндекс Денег.
    
    // Параметры, которые Вам пригодятся
    // $_POST['operation_id'] - номер операции (огромное число, в БД советую создать поле varchar 255)
    // $_POST['amount'] - количество денег, которые поступят на счет получателя
    // $_POST['withdraw_amount'] - количество денег, которые будут списаны со счета покупателя
    // $_POST['datetime'] - тут понятно, дата и время оплаты
    // $_POST['sender'] - если оплата производится через Яндекс Деньги, то этот параметр содержит номер кошелька покупателя
    // $_POST['label'] - лейбл, который мы указывали в форме оплаты
    // $_POST['email'] - email покупателя (доступен только при использовании https://)

    // Генерация ключа, для проверки подлинности пришедших к нам данных
    $sha1 = sha1( $_POST['notification_type'] . '&'. $_POST['operation_id']. '&' . $_POST['amount'] . '&643&' . $_POST['datetime'] . '&'. $_POST['sender'] . '&' . $_POST['codepro'] . '&' . $secret_key. '&' . $_POST['label'] );

    //Проверка на подлиность уведомления
    if ($sha1 != $_POST['sha1_hash'] ) {
        // Код на случай если ключи не совпадают...значит либо пришло чтото левое,
        //либо Вы не правильно сгенерировали ключ
    } else {
        // тут код на случай, если проверка прошла успешно
        // Делаем чтото в базе, записываем историю, отсылаем пользователю уведомление об успешной покупке и т.п.

        //Яндекс высылает повторно еще 2 копии того же уведомления, проверяем, если мы уже записали такое уведомление в БД, то игнорируем его копии.
        $query = "SELECT * FROM donate_info WHERE account_id = ? AND datetime = ?";
        $result = $pdo->prepare($query);
        $params = [(int)$_POST['label'], $_POST['datetime']];
        $result->execute($params);

        if ( $result->rowCount() == 1 ) {
            exit();
        }
        
        //Создаем таблицу в БД с инфой по перечислениям
        $result = $pdo->query('SHOW TABLES LIKE "donate_info"');
        if ( $result->rowCount() == 0 ) {
            $query = 'CREATE TABLE donate_info (
                account_id INT,
                label VARCHAR(20),
                nickname VARCHAR(20),
                notification_type TEXT,
                operation_id TEXT,
                amount FLOAT,
                datetime TEXT
                )';
            $pdo->query($query);
        }

        //Если сумма меньше 100 то тикет не даем
        if ($_POST['withdraw_amount'] >= 100) {
            $query = 'UPDATE leagueplayers SET tickets = tickets + 1 WHERE account_id = ?';		
            $result = $pdo->prepare($query);
            $params = [(int)$_POST['label']];
            $result->execute($params);
        }
      
        //Записываем перечисления
        $query = "INSERT INTO donate_info (account_id, label, nickname, notification_type, operation_id, amount, datetime)
        VALUES (
        :account_id,
        :label,
        (SELECT nickname FROM leagueplayers WHERE account_id = :account_id_2),
        :notification_type,
        :operation_id,
        :amount,
        :datetime)
        ";
        
        $result = $pdo->prepare($query);
        $params = [
        ':account_id' => (int)$_POST['label'], 
        ':label' => (int)$_POST['label'],
        ':account_id_2' => (int)$_POST['label'], 
        ':notification_type' => $_POST['notification_type'],
        ':operation_id' => $_POST['operation_id'],
        ':amount' => $_POST['amount'],
        ':datetime' => $_POST['datetime']
        ];
        $result->execute($params);
    }
}

?>
<main>
<?php include_once 'header.php' ?>
    <div class="donate_info">
        <p>Ваша благотворительность, это:</p>
        <div class="bonuses">
            <div class="list_of_bonuses">
                <img class="bonuses_img" src="/images/donation/ticket.png">
                <p>1 билет в подлигу Normal</p>
            </div>
            <div class="list_of_bonuses">
                <img class="bonuses_img" src="/images/donation/money.png">
                <p>Увеличение призового фонда</p>
            </div>
            <div class="list_of_bonuses">
                <img class="bonuses_img" src="/images/donation/support.png">
                <p>Поддержка проекта</p>
            </div>
        </div>
        <form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
            <input type="hidden" name="receiver" value="4141346366637193">
            <input type="hidden" name="formcomment" value="Поддержка проекта Wotleague">
            <input type="hidden" name="short-dest" value="Поддержка проекта Wotleague">
            <input type="hidden" name="label" value="<?= $_SESSION['account_id']?>">
            <input type="hidden" name="quickpay-form" value="donate">
            <input type="hidden" name="targets" value="Поддержка проекта Wotleague">
            <input type="hidden" name="sum" value="100" data-type="number">
            <input type="hidden" name="paymentType" value="AC">
            <input type="hidden" name="successURL" value="https://wotleague.ru">
            <input class="donate_button" type="submit" value="Поддержать">
        </form>
    </div>
</main>
	<?php include_once 'footer.php' ?>	
</body>
</html>