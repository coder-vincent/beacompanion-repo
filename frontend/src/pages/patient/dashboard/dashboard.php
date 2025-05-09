<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once(__DIR__ . '/../../../../auth/dbconnect.php');

// echo '<pre>';
// print_r($_SESSION['user']);
// echo '</pre>';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $providedToken = $_GET['token'];
    $stmt = $pdo->prepare('SELECT id, email, name, role, auth_token, auth_token_expiry FROM users WHERE auth_token IS NOT NULL');
    $stmt->execute();

    $user = null;
    while ($row = $stmt->fetch()) {
        if (password_verify($providedToken, $row['auth_token'])) {
            if (strtotime($row['auth_token_expiry']) >= time()) {
                $user = $row;
                break;
            }
        }
    }

    if (!$user) {
        echo '<script>window.location.href = "/thesis_project";</script>';
        exit();
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
        'role' => $user['role'],
        'plain_token' => $providedToken,
    ];
}

$fullName = $_SESSION['user']['name'] ?? 'Guest';
$token = $_SESSION['user']['plain_token'] ?? '';
$firstName = explode(' ', trim($fullName))[0] ?? 'Guest';
?>

<div id="patient-page">
    <div class="welcome-reveal">
        <div class="welcome-container">
            <div class="welcome-loader">
                <h1>Hi, <?php echo htmlspecialchars($firstName); ?>!👋</h1>
            </div>
            <div class="welcome-overlay">
                <?php for ($i = 1; $i <= 20; $i++): ?>
                    <div class="block block-<?php echo $i; ?>"></div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <h2>You are now logged in</h2>

    <div id="patient-dashboard">
        <div class="dashboard-content">
            <!-- <h3>Hello, <?php echo htmlspecialchars($firstName); ?>! <?php echo htmlspecialchars($token); ?></h3> -->
            <?php
            include __DIR__ . '/../../../components/navbar/navbar.php';
            include __DIR__ . '/../../../components/sidebar/sidebar.php';
            ?>

            <main>
                <h1>Hello World</h1>
                <p> Lorem ipsum dolor sit amet consectetur adipisicing elit. Reiciendis, cum nulla! Quo animi minus
                    quaerat ipsa. Magni laboriosam ipsam id perferendis optio deserunt dolorem corrupti quidem molestiae
                    veritatis dignissimos, facere, architecto amet mollitia minus! Atque, consequatur laborum. Quisquam
                    quidem dolores quae nesciunt. Nisi aliquid placeat, odio magnam iusto ad, illo alias accusantium
                    officia libero deserunt perspiciatis, exercitationem necessitatibus. Iusto dolor laborum facilis
                    numquam, animi incidunt excepturi dignissimos nobis ullam ea illo perspiciatis natus dolore
                    quibusdam minima tempore quidem iste beatae ipsam voluptatum doloremque recusandae? Expedita eaque
                    beatae magni reiciendis sequi voluptates nisi quos aperiam consectetur consequuntur veniam, nemo a
                    unde recusandae corrupti commodi! Rerum a assumenda itaque praesentium iusto quis veritatis illum
                    amet omnis asperiores alias ad unde eos tenetur aut pariatur ipsa, dignissimos enim laborum corrupti
                    suscipit! Nulla, architecto aut, recusandae saepe, dolore nisi maiores quisquam aliquam iure
                    explicabo animi sint fuga! Nesciunt autem tenetur nihil, placeat repudiandae accusantium quas
                    voluptatem iste obcaecati omnis quos praesentium fugit id voluptate, deleniti itaque nostrum nisi
                    harum esse quam iure? Facilis, ducimus. Vero laboriosam amet mollitia, deserunt itaque quaerat esse
                    ea odio voluptate tempore inventore odit praesentium nihil natus officia assumenda sapiente culpa
                    voluptatem dolor dolores, perferendis quibusdam obcaecati id! Cum eveniet molestiae, similique at
                    delectus nulla sit obcaecati facilis dicta vero culpa recusandae saepe expedita libero possimus eius
                    excepturi incidunt adipisci sed doloremque itaque. Iure, odio animi perspiciatis, ab obcaecati quae
                    voluptatibus laudantium quod placeat exercitationem fuga, dolorem non architecto quam harum.
                    Consectetur, nulla iure. Magni doloremque dolor excepturi eum eaque porro quidem, earum optio totam
                    illo et veritatis corporis doloribus eius, voluptatem magnam. Molestiae odio nam dolorum,
                    necessitatibus dolorem vero ea eligendi quos ab aliquid assumenda quaerat omnis, beatae, ut iste
                    explicabo. Harum obcaecati voluptates ad iste consequuntur? Laboriosam voluptatum dolore ab velit
                    reiciendis placeat, quos animi doloremque delectus sed illum facilis atque illo rem corrupti quod
                    consequuntur! Esse sed, debitis odio nostrum autem modi est illum suscipit ex dolorem optio vitae
                    odit. Necessitatibus, quos. Id expedita aspernatur nobis magni dolorum. Aliquid quibusdam dolores
                    doloremque odio dicta hic laboriosam deserunt corrupti? Quisquam adipisci, minima, corrupti at quod
                    vitae, impedit molestiae ullam nemo harum optio. Sequi obcaecati accusantium facere nulla aliquam
                    quos itaque cum nesciunt aperiam a beatae amet porro accusamus illo sed quisquam sint optio,
                    voluptas molestias praesentium nam sunt. Quia officia est natus, et iusto ab repudiandae facere
                    illum alias, enim provident dolore pariatur aliquam cumque mollitia culpa explicabo, rem at tempora
                    nam minus totam. Dolores sit quam voluptatem quibusdam a dolorum rem, modi nobis id, dolor minus
                    quia? Cum minima, consequatur magnam id perferendis et ducimus quidem officia incidunt rem
                    voluptatum aut, similique animi magni, voluptatibus consectetur accusamus dolorum quae unde corporis
                    blanditiis harum molestias. Ea quam dignissimos laudantium maiores porro quos iste, saepe eum,
                    aspernatur reprehenderit velit commodi ratione, sed dolores officiis perferendis praesentium minus
                    tempora tempore nemo inventore distinctio repellat eius nobis. Pariatur, rerum corporis. Soluta
                    inventore explicabo repellat eos tenetur officia, minima sit necessitatibus at accusamus sed,
                    praesentium alias dolorum odit, nulla magni libero ex deserunt voluptates nihil ipsam deleniti iure?
                    Culpa placeat ut obcaecati sint esse nihil eum. Atque rem corporis repellat cum voluptate temporibus
                    a vel debitis, numquam quas ducimus qui, pariatur necessitatibus aspernatur libero ab est inventore
                    quod voluptatem cumque minima nostrum eveniet? Laboriosam minima veniam quibusdam quos debitis,
                    autem necessitatibus expedita nulla officia non saepe vel quis. Architecto eius, iste quasi aut
                    tempore iure voluptatum amet animi cumque reprehenderit praesentium, in non error quam! Qui culpa
                    provident, blanditiis magnam quae vel sit officiis quasi, tempora deleniti molestiae! Excepturi
                    explicabo atque debitis beatae natus consectetur cum pariatur quasi itaque deleniti quia incidunt
                    odio unde cumque eveniet dolorem eaque possimus, adipisci nihil quos! Ipsam obcaecati tenetur
                    perspiciatis temporibus ducimus similique deleniti eius necessitatibus eum ut vitae impedit atque,
                    amet molestias enim praesentium, magnam omnis maxime molestiae dicta vel! Rerum veniam ratione fuga
                    totam, maiores amet sint quia laborum ullam. Unde nulla dolore voluptatum harum blanditiis? Sequi,
                    nisi eius. Cupiditate deleniti itaque pariatur iure reiciendis nesciunt, architecto rem.
                    Consectetur, iste porro. Ab repellat ipsam similique deleniti officia, excepturi quasi. Repellat
                    omnis architecto, facere deserunt nemo molestiae odit cum. Dolorem quod saepe aliquid libero neque
                    temporibus! Deleniti eius iusto, temporibus, voluptatibus quaerat quos neque error nulla possimus
                    minus rem culpa ab. Molestias id, facere magnam neque impedit placeat error porro dignissimos beatae
                    velit, minus fugiat delectus voluptates esse, voluptate mollitia est enim consectetur veritatis
                    laudantium et. Porro, ex maiores? Recusandae voluptate odit totam minus nam eaque adipisci
                    assumenda, sequi obcaecati itaque sit id quisquam facilis! Consectetur dolorum ut quod non, adipisci
                    similique saepe distinctio, sapiente, velit necessitatibus alias. Maxime quia dicta ipsum
                    perferendis. Molestias sequi dolorum cumque quia ipsum quo excepturi facere eveniet facilis? Eos
                    necessitatibus qui fugit doloremque facilis minima officia recusandae distinctio voluptate
                    laudantium quas mollitia maiores tenetur eius voluptatum ipsam consequuntur sapiente, enim
                    asperiores autem provident! Unde beatae ut consequatur, eveniet ipsam numquam possimus, suscipit,
                    fugiat doloribus cum pariatur eos perspiciatis. Aspernatur vel repellendus beatae repellat, saepe
                    est cumque ducimus consectetur harum nisi similique fuga dicta fugit id doloremque voluptatem illum
                    ipsum blanditiis odio velit quia itaque! Et vitae adipisci facere odio odit, aut aliquid dolorum
                    laudantium sed eius maxime veniam consequuntur cupiditate praesentium nam. Adipisci impedit
                    repellendus dolorem placeat molestiae blanditiis natus assumenda mollitia optio rem exercitationem
                    doloribus aliquid possimus tenetur fugit eius esse tempore quos consectetur fugiat officiis
                    voluptatum, magni error? Quo repellat minima cum. Itaque quod libero earum repudiandae ducimus
                    aperiam possimus est minus nulla, veritatis minima nihil soluta suscipit enim facere. Dolorem,
                    facilis sit dignissimos, quibusdam animi accusamus repellat laborum eveniet ullam nesciunt vero est,
                    quisquam repudiandae enim ratione mollitia atque? Incidunt itaque optio at commodi facilis hic qui
                    porro repellat pariatur excepturi id saepe consequuntur eos dolorum doloribus, exercitationem
                    laborum voluptate placeat repellendus facere. Amet, labore eos placeat dolorem fuga iure! Dolor unde
                    hic perferendis cupiditate voluptatibus repellat assumenda cumque ullam quidem qui quod, earum
                    similique exercitationem temporibus nam animi architecto nemo delectus. Unde repellat necessitatibus
                    voluptatibus reprehenderit, labore cum corporis in. Architecto facere unde corrupti voluptate. Eos
                    impedit beatae earum laborum voluptatum sapiente cumque animi eaque porro distinctio? Harum vitae
                    tenetur distinctio odit, maiores consequuntur qui. Praesentium totam, sed ipsum placeat, magni
                    cupiditate assumenda vel laboriosam consequatur culpa saepe, quidem est doloribus quo nobis illo et
                    dolorem! Molestiae illum debitis corrupti laborum, nobis corporis, voluptates omnis doloribus quas
                    laboriosam ducimus! Quae, autem iusto atque ab ea reiciendis perferendis odio voluptatem ipsum
                    sapiente minus. Iste minima deserunt molestias expedita non nulla, saepe sint dicta, voluptate quia
                    quas voluptatibus recusandae assumenda provident voluptatem iusto ea vel libero molestiae beatae
                    quibusdam nisi quaerat. Consequatur vel voluptatum fugit doloremque veniam non earum facilis
                    corporis aperiam culpa exercitationem praesentium quia repellendus, labore autem delectus iusto quos
                    voluptatibus veritatis modi atque. Nam, quidem! At, alias. Earum ipsa eveniet doloribus asperiores
                    repellendus tenetur fugiat odit quod quos excepturi quasi, sed voluptate suscipit libero rem labore
                    eaque saepe a itaque vel perspiciatis quibusdam aspernatur nemo. Beatae sint sunt distinctio nisi
                    aspernatur, at cum. Similique, itaque deserunt, vel quos, commodi nostrum amet molestias doloremque
                    quae voluptas officiis sit ea reprehenderit! Odio nulla quae voluptatum, ipsam quia iste laborum
                    architecto inventore magnam vero excepturi, consequuntur sunt velit, non rem qui cumque. Ipsum,
                    impedit soluta! Iste, omnis eum, quae eligendi consequatur labore reiciendis pariatur dolorem quos a
                    illum. Provident quia repellat maiores iste neque pariatur ab beatae, doloribus sit soluta vero,
                    odio officia vel suscipit veritatis maxime eius, necessitatibus illum similique enim expedita. Culpa
                    atque in molestias accusamus illum voluptate, doloremque optio ratione quia animi at, itaque ex quo
                    recusandae maxime debitis non quisquam autem! Cupiditate in, perspiciatis, quod dolores ipsam
                    doloremque accusamus dolorum repudiandae quis repellendus earum vel beatae eaque voluptates hic
                    voluptatum nihil? Doloribus aliquam itaque porro incidunt omnis nihil natus saepe minima in
                    distinctio ratione tenetur numquam, facilis non eligendi perferendis nam illum, eum, eos quo
                    tempore. Animi officiis sit cupiditate itaque qui laudantium soluta, voluptate, dolores perferendis
                    quae excepturi dignissimos ipsam dolor incidunt id, harum voluptatibus corporis. Perferendis omnis
                    dolores quaerat numquam ratione fugiat. Ea maxime at atque libero commodi aliquid vel nisi, officia
                    pariatur, officiis doloribus reprehenderit itaque? Doloremque consequatur unde laboriosam illum
                    perspiciatis, pariatur itaque dignissimos rerum quasi velit quidem ex! Quaerat sit aliquid sed
                    architecto rerum animi eius nam ea molestias. Nulla unde recusandae quasi tempore suscipit,
                    veritatis facilis, pariatur tempora, cupiditate impedit cumque eos blanditiis magni ipsa quae. Quos
                    quae vel, veritatis amet repudiandae ut architecto, distinctio dolore aperiam cum iusto nam ex rem,
                    minus consequuntur asperiores dolores cupiditate? Vel vero veritatis libero accusamus aliquam
                    inventore architecto, unde asperiores pariatur perspiciatis natus! Quasi officiis quisquam ipsam
                    nobis hic omnis, ex harum vero nulla eligendi aperiam iusto commodi incidunt exercitationem
                    repudiandae similique soluta quo explicabo fugit. Eaque neque inventore veritatis tempore alias ipsa
                    enim vitae, perferendis quas. Accusamus neque expedita temporibus non error, autem laborum nulla
                    maxime numquam cumque quisquam excepturi facere! Labore, excepturi laboriosam! Culpa quasi suscipit
                    tempora laboriosam, ad consequuntur consequatur earum totam facere perferendis et, eaque ducimus
                    quod magni voluptas impedit debitis praesentium officiis! Explicabo nobis sed odit adipisci
                    obcaecati accusamus amet maxime harum ab reiciendis, quas odio! Molestiae, aliquam sequi. Eaque rem
                    cum iste, unde explicabo architecto recusandae laborum sit, odit pariatur corrupti natus! Officia
                    exercitationem consequatur, earum in quibusdam voluptate vel debitis cum laudantium deserunt
                    doloremque? Voluptas officia a dolores dolor sit placeat iusto facere pariatur! Placeat temporibus
                    nesciunt, optio voluptatem voluptates sint similique ipsam eligendi sunt saepe, reprehenderit sed
                    excepturi veritatis ipsa esse necessitatibus at aliquam praesentium. Vero tempora corrupti aliquid
                    provident veniam tenetur qui illum, suscipit, modi, voluptatum ratione! Sunt unde quia perferendis
                    asperiores esse eum quam similique atque labore, vitae omnis cupiditate minima, illum possimus
                    consequatur officiis a deleniti inventore repudiandae tenetur, magni at? Quo quis, optio minus
                    assumenda neque nesciunt ipsa libero repellat, earum et totam similique sapiente in vitae cum?
                    Dolorem id aut omnis perferendis quo sequi, eveniet excepturi quidem voluptatem dolorum, temporibus,
                    impedit laudantium ea. Veniam ab eligendi iure sed praesentium. Harum quis eum facere assumenda
                    beatae ea rerum officia, amet dolores eligendi id laboriosam, quo explicabo odio, a nihil quod
                    excepturi? Sit voluptates ut eius quaerat sint sequi dolore quo suscipit. Provident impedit magnam
                    totam tenetur, recusandae, aperiam consequuntur temporibus incidunt quis necessitatibus non,
                    repellendus quo inventore quos debitis accusantium ad sed. Voluptates quam provident deleniti
                    accusantium! Ea aperiam odio blanditiis dolorem voluptates ullam praesentium tenetur unde.
                    Repellendus, fugit impedit doloribus corporis id cum rerum quisquam, in doloremque repudiandae
                    officiis nostrum reprehenderit minima saepe nesciunt molestiae delectus minus explicabo? Eum debitis
                    fugiat ducimus deserunt vel officia expedita dolor cum obcaecati odio inventore, repellat error nisi
                    ab quam eveniet non, officiis reprehenderit blanditiis reiciendis. Labore sunt ratione cum
                    perferendis aspernatur corporis tenetur ullam, eum aliquid debitis aperiam est delectus eos vero
                    minima nemo recusandae distinctio illo? Rerum aspernatur doloremque commodi iure itaque nobis ut,
                    eos voluptas dolores quidem architecto, eveniet alias quod unde, neque explicabo sunt perspiciatis
                    quis at id facere eligendi illo provident saepe! Ullam perferendis, placeat, qui libero quam
                    officiis vero dolor doloremque recusandae illo eum! Maxime deserunt quae, quas eveniet laboriosam,
                    voluptatibus inventore odio cum vel itaque consequuntur a? Dolor harum illum, aspernatur animi
                    voluptatum exercitationem fugiat eveniet qui quis nam nulla laboriosam hic commodi aliquam
                    consequuntur maiores itaque officiis, consequatur repellendus inventore. Voluptatem quia libero quis
                    repellat ipsa nam magnam ab laborum id soluta odit fugiat doloremque quisquam, nihil, rem ad,
                    repudiandae cum corporis molestias. Fuga quia temporibus soluta recusandae blanditiis magni quas
                    ratione quibusdam aspernatur, sunt ea voluptatum ad aliquam ipsum non commodi iure quo quod
                    voluptatibus veritatis aut quae dolores. Repudiandae sunt exercitationem, minus nisi molestias odit
                    doloribus praesentium impedit, vitae distinctio delectus consectetur officiis est amet facere ea ab
                    illo accusamus corrupti, quae temporibus. Aspernatur iusto voluptates deserunt molestias, sequi ipsa
                    id deleniti! Dolor pariatur omnis quos, eius ipsam molestiae animi, in atque esse nobis cum labore
                    quam repellendus? Similique nemo tenetur quaerat, eligendi vero laborum nihil odio neque culpa alias
                    eveniet temporibus nostrum accusamus amet porro vitae repellat tempora! Quam expedita voluptate
                    provident, soluta similique commodi. Illum veniam porro ipsa voluptatum.
                </p>
            </main>

            <?php
            include __DIR__ . '/../../../components/footer/footer.php';
            ?>
        </div>
    </div>