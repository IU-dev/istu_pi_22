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
        header("Content-Type: application/json");
        $login = $_GET['login'];
        $passwd = md5($_POST['password']);
        $usr = $db->select('users', "id = '" . $login . "' AND password = '" . $passwd . "'");
        if ($usr['password'] == $passwd) {
            $result['answer'] = "OK";
            $group = $db->select('groups', "id = '" . $usr['group_id'] . "'");
            $result['group'] = $group['name'];
            $result['userid'] = (int)$usr['id'];
            $result['token'] = rand('10000000', '99999999');
            if (file_exists("avatars/" . $usr['id'] . ".jpg")) $result['avatar_link'] = "https://eis.it-lyceum24.ru/avatars/" . $usr['id'] . ".jpg";
            else $result['avatar_link'] = "https://eis.it-lyceum24.ru/avatars/placeholder.png";
            $result['fio'] = $userTools->fio($usr['id'], false);
            $result['uid'] = $usr['id'];
            $ug = $db->select('periods', "id = '" . $tool->getGlobal('default_period') . "'");
            $result['uch_god'] = $ug['name'];
            $data['token'] = (int)$result['token'];
            $u = $db->update($data, 'users', "id = '" . $_GET['login'] . "'");
        } else $result['answer'] = "AUTH_ERROR";
        echo json_encode($result);
    } # working Marina
    } # с этого места идут $_GET['act'], требующие авторизации юзверя с использованием токена
    else {
        $p = $db->select('users', "id = '" . $_GET['uid'] . "'");
        if ($_GET['token'] == $p['token']) {
            if ($_GET['act'] == "getSkills") {
                header("Content-Type: application/json");
                $result['answer'] = "OK";
                $skills = $db->select_fs('r_skills_bids', "period_id = '" . $tool->getGlobal('default_period') . "' AND user_id = '" . $p['id'] . "'");
                foreach ($skills as $key => $skill) {
                    $sn = $db->select('r_skills', "id = '" . $skill['skill_id'] . "'");
                    $result['skills'][$key]['name'] = $sn['name'];
                    $result['skills'][$key]['value'] = (int)$skill['value'];
                    $result['skills'][$key]['max_value'] = (int)$sn['max_value'];
                }
            } else if ($_GET['act'] == "getAchievements") {
                header("Content-Type: application/json");
                $result['answer'] = "OK";
                $achs = $db->select_desc_fs('r_achievements_bids', "period_id = '" . $tool->getGlobal('default_period') . "' AND user_id = '" . $p['id'] . "'");
                foreach ($achs as $key => $ach) {
                    $sn = $db->select('r_achievements', "id = '" . $ach['ach_id'] . "'");
                    $result['achievements'][$key]['name'] = $sn['name'];
                    $result['achievements'][$key]['picture'] = $sn['dir'];
                    $result['achievements'][$key]['description'] = $sn['descr'];
                    $result['achievements'][$key]['datetime'] = $ach['datetime'];
                    $result['achievements'][$key]['reason'] = $ach['reason'];
                    $by = $db->select('users', "id = '" . $ach['given_id'] . "'");
                    $result['achievements'][$key]['given_by'] = '(' . $by['id'] . ') ' . $by['f'] . ' ' . $by['i'] . ' ' . $by['o'];
                }
            } else if ($_GET['act'] == "getBalance") {
                header("Content-Type: application/json");
                $result['answer'] = "OK";
                $accs = $db->select_desc_fs('r_accs_bids', "period_id = '" . $tool->getGlobal('default_period') . "' AND user_id = '" . $p['id'] . "'");
                foreach ($accs as $key => $acc) {
                    $sn = $db->select('r_accs', "id = '" . $acc['acc_id'] . "'");
                    $result['accs'][$key]['name'] = $sn['name'];
                    $result['accs'][$key]['icon'] = $sn['icon'];
                    $result['accs'][$key]['value'] = (int)$acc['value'];
                }
            } else if ($_GET['act'] == "getLogs") {
                header("Content-Type: application/json");
                $result['answer'] = "OK";
                $accs = $db->select_desc_fs('r_logs', "period_id = '" . $tool->getGlobal('default_period') . "' AND user_id = '" . $p['id'] . "'");
                date_default_timezone_set($tool->getGlobal('timezone'));
                foreach ($accs as $key => $acc) {
                    $result['logs'][$key]['datetime'] = date("d.m.Y H:i:s", strtotime($acc['datetime'] . " GMT"));
                    $result['logs'][$key]['who'] = $userTools->fio($acc['who_id']);
                    $result['logs'][$key]['action'] = $acc['action'];
                    $result['logs'][$key]['text'] = $acc['text'];
                }
                date_default_timezone_set("GMT");
            } else if ($_GET['act'] == "getPortfolio") {
                header("Content-Type: application/json");
                $result['answer'] = "OK";
                $accs = $db->select_desc_fs('r_portfolio_bids', "period_id = '" . $tool->getGlobal('default_period') . "' AND user_id = '" . $p['id'] . "'");
                date_default_timezone_set($tool->getGlobal('timezone'));
                foreach ($accs as $key => $acc) {
                    $result['portfolio'][$key]['id'] = (int)$acc['id'];
                    $result['portfolio'][$key]['link'] = $acc['link'];
                    $p = $db->select('r_portfolio', "id = '" . $acc['portfolio_id'] . "'");
                    $result['portfolio'][$key]['level_name'] = $p['name'];
                    $result['portfolio'][$key]['name'] = $acc['name'];
                    $result['portfolio'][$key]['descr'] = $acc['descr'];
                    if ($acc['state'] == "0") {
                        $result['portfolio'][$key]['state'] = "Новый";
                    } else if ($acc['state'] == "1") {
                        $result['portfolio'][$key]['state'] = "Оценено";
                        $result['portfolio'][$key]['acc_who'] = $userTools->fio($acc['acc_who']);
                        $result['portfolio'][$key]['acc_when'] = date("d.m.Y H:i:s", strtotime($acc['acc_when'] . " GMT"));
                        $result['portfolio'][$key]['acc_descr'] = $acc['acc_descr'];
                    } else if ($acc['state'] == "2") {
                        $result['portfolio'][$key]['state'] = "Отказано";
                        $result['portfolio'][$key]['acc_who'] = $userTools->fio($acc['acc_who']);
                        $result['portfolio'][$key]['acc_when'] = date("d.m.Y H:i:s", strtotime($acc['acc_when'] . " GMT"));
                        $result['portfolio'][$key]['acc_descr'] = $acc['acc_descr'];
                    }
                }
                date_default_timezone_set("GMT");
            } else if ($_GET['act'] == "sendPortfolio") {
                if (isset($_GET['name']) && isset($_GET['descr']) && isset($_GET['link']) && isset($_GET['pid'])) {
                    $result['answer'] = "OK";
                    $data['period_id'] = "'" . $tool->getGlobal('default_period') . "'";
                    $data['user_id'] = "'" . $_GET['uid'] . "'";
                    $data['portfolio_id'] = "'" . $_GET['pid'] . "'";
                    $data['link'] = "'" . $_GET['link'] . "'";
                    $data['name'] = "'" . $_GET['name'] . "'";
                    $data['descr'] = "'" . $_GET['descr'] . "'";
                    $data['state'] = "'0'";
                    $data['id'] = $db->insert($data, 'r_portfolio_bids');
                } else $result['answer'] = "ERROR";
            }
        } else $result['answer'] = "TOKEN_ERROR";
        echo json_encode($result);
    }
} else {
    $result['answer'] = "HACKING_ATTEMPT";
    die(json_encode($result));
}
?>