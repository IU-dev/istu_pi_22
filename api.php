<?php
//login.php
require_once 'includes/global_api.inc.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
//header("Content-Type: application/json");
$page = "api.php";
$error = "";
$pid = "";

if (isset($_GET['act'])) {
    if ($_GET['act'] == "auth") {
        #working Anya
    } else if ($_GET['act'] == "request") {
        echo 'INDEV';
    } else if ($_GET['act'] == "notify") {
        echo 'INDEV';
    } else if ($_GET['act'] == "getPortfolioTypes") {
        header("Content-Type: application/json");
        $result['answer'] = "OK";
        $accs = $db->select_desc_fs('r_portfolio', "id != 0");
        foreach ($accs as $key => $acc) {
            $result['types'][$key]['id'] = (int)$acc['id'];
            $result['types'][$key]['name'] = $acc['name'];
            $p = json_decode($acc['weight']);
            foreach ($p as $key2 => $a) {
                $v = $db->select('r_accs', "id = '" . $key2 . "'");
                $result['types'][$key]['vaults'][$key2]['name'] = $v['name'];
                $result['types'][$key]['vaults'][$key2]['value'] = (int)$a;
            }
        }
        echo json_encode($result);
    } else if ($_GET['act'] == "getlog") {
        echo 'INDEV';
    } else if ($_GET['act'] == "regenerate" && $_GET['secret'] == "bondartop14072003") {
        $grps = $db->select_fs('groups', "id = '" . $_GET['group_id'] . "'");
        foreach ($grps as $grp) {
            echo '<strong>Группа ' . $grp['name'] . '</strong><br>';
            $usrs = $db->select_fs('users', "group_id = '" . $grp['id'] . "' AND state = '1'");
            foreach ($usrs as $usr) {
                if ($_GET['clear_password'] == "1") {
                    $np = random_int(100000, 999999);
                    $data['password'] = "'" . md5($np) . "'";
                }
                $token = bin2hex(random_bytes(16));
                $token2 = bin2hex(random_bytes(16));
                $data['token'] = "'" . $token . "'";
                $data['token2'] = "'" . $token2 . "'";
                $b = $db->update($data, 'users', "id = '" . $usr['id'] . "'");
                echo $usr['f'] . ';' . $usr['i'] . ';' . $usr['o'] . ', ваш ID - ' . $usr['id'] . ', ваш новый пароль - ' . $np . ';';
                /* if ($usr['admin'] == '0') echo 'Обучающийся';
                else if ($usr['admin'] == '1') echo 'Дежурный';
                else if ($usr['admin'] == '2') echo 'Сотрудник';
                else if ($usr['admin'] == '3') echo 'Секретарь';
                else if ($usr['admin'] == '4') echo 'Зам. директора';
                else if ($usr['admin'] == '5') echo 'Директор';
                else if ($usr['admin'] == '9') echo 'Администратор'; */
                echo '<br>';
            }
            echo '<br>';
        }
    } else if ($_GET['act'] == "getUsersDataCheck" && $_GET['secret'] == "bondartop14072003") {
        $grp = $db->select('groups', "id = '" . $_GET['gid'] . "'");
        echo '<strong>Группа: ' . $grp['name'] . '</strong><br><br>';
        $usrs = $db->select_fs('users', "group_id = '" . $_GET['gid'] . "' ORDER BY f ASC, i ASC");
        foreach ($usrs as $usr) {
            if ($_GET['showid'] == "1") echo '(' . $usr['id'] . ') ' . $usr['f'] . ' ' . $usr['i'] . ' ' . $usr['o'] . '<br>';
            else if ($_GET['showid'] == "0") echo $usr['f'] . ' ' . $usr['i'] . ' ' . $usr['o'] . '<br>';
        }
        echo '<hr>Выписка из Единой информационной системы<br>МБОУ "ИТ-лицей №24"';
        echo '<br>' . date("d.m.Y H:i:s");
    } else if ($_GET['act'] == "getIrbisData" && $_GET['secret'] == "bondartop14072003") {
        $usrs = $db->select_fs('users', "id != '0'");
        echo 'getIrbisData: Генерация данных для системы ИРБИС64.<br><strong>Внимание! Не забудьте перекодировать txt файл в кодировку Windows!!!</strong><hr>';
        $i = 1;
        foreach ($usrs as $usr) {
            $group = $db->select('groups', "id = '" . $usr['group_id'] . "'");
            echo '#920: RDR<br>';
            echo '#10: ' . $usr['f'] . '<br>';
            echo '#11: ' . $usr['i'] . '<br>';
            echo '#12: ' . $usr['o'] . '<br>';
            echo '#30: ' . $usr['id'] . '<br>';
            echo '#50: ' . $group['name'] . '<br>';
            echo '#907: ^A20190813^B1<br>';
            echo '*****<br>';
            $i = $i + 1;
        }
        $i = $i - 1;
        echo '<hr>Операция завершена. Всего пользователей: ' . $i . '.';
    } else if ($_GET['act'] == "getLinksForParents") {
        $ruid = $db->select('users', "id = '" . $_GET['ruid'] . "'");
        if (isset($_GET['token2']) && isset($_GET['ruid']) && $_GET['token2'] == $ruid['token2']) {
            $group = $db->select('groups', "id = '" . $_GET['gid'] . "'");
            $users = $db->select_fs('users', "group_id = '" . $_GET['gid'] . "' ORDER BY f ASC");
            foreach ($users as $u) {
                echo '<strong>Единая информационная система МБОУ "ИТ-лицей №24"<br>Доступ к внесению первичных персональных данных</strong><br><br>';
                echo 'ФИО: ' . $u['f'] . ' ' . $u['i'] . ' ' . $u['o'] . ' (' . $group['name'] . ')<br>';
                echo 'Перейдите по QR-коду для внесения данных. Внести данные можно только один раз.<br>Никому не передавайте данный QR-код!<br><br>';
                echo 'https://eis.it-lyceum24.ru/info_an.php?id=' . $u['id'] . '&gid=0&firstpass=' . $u['token2'] . '<br>';
                echo '<img src="https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=https%3A%2F%2Feis.it-lyceum24.ru%2Finfo_an.php%3Fid%3D' . $u['id'] . '%26gid%3D0%26firstpass%3D' . $u['token2'] . '&choe=UTF-8" title="Link to Google.com" />';
                echo '<hr>';
            }
        } else {
            http_response_code(403);
            echo '<strong>Ошибка 403 - Доступ запрещен</strong><br><br>Вы попытались получить QR-коды с некорректным ключом безопасности №2. Обратитесь к техническому специалисту.';
        }
    } else if ($_GET['act'] == "getLinkForParent") {
        $ruid = $db->select('users', "id = '" . $_GET['ruid'] . "'");
        if (isset($_GET['token2']) && isset($_GET['ruid']) && $_GET['token2'] == $ruid['token2']) {
            $u = $db->select('users', "id = '" . $_GET['uid'] . "'");
            $group = $db->select('groups', "id = '" . $u['group_id'] . "'");
            echo '<strong>Единая информационная система МБОУ "ИТ-лицей №24"<br>Доступ к внесению первичных персональных данных</strong><br><br>';
            echo 'ФИО: ' . $u['f'] . ' ' . $u['i'] . ' ' . $u['o'] . ' (' . $group['name'] . ')<br>';
            echo 'Перейдите по QR-коду для внесения данных. Внести данные можно только один раз.<br>Никому не передавайте данный QR-код!<br><br>';
            echo 'https://eis.it-lyceum24.ru/info_an.php?id=' . $u['id'] . '&gid=0&firstpass=' . $u['token2'] . '<br>';
            echo '<img src="https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=https%3A%2F%2Feis.it-lyceum24.ru%2Finfo_an.php%3Fid%3D' . $u['id'] . '%26gid%3D0%26firstpass%3D' . $u['token2'] . '&choe=UTF-8" title="Link to Google.com" />';
            echo '<hr>';
        } else {
            http_response_code(403);
            echo '<strong>Ошибка 403 - Доступ запрещен</strong><br><br>Вы попытались получить QR-коды с некорректным ключом безопасности №2. Обратитесь к техническому специалисту.';
        }
    } # с этого места идут $_GET['act'], требующие авторизации юзверя с использованием токена
    else {
        #working Bondar
    }
} else {
    $result['answer'] = "HACKING_ATTEMPT";
    die(json_encode($result));
}
?>