<?php
defined('ABSPATH') || exit;

global $profile, $card;
?>

    <div class="wt-content wt-margin-bottom-x2">
        <?php echo wp_kses_post($post->post_content); ?>
    </div>

    <form data-route="wt/v1/crm/friends" method="post" autocomplete="off">
        <h1 class="wt-h6 wt-upper wt-margin-bottom-x2">Refer a friend now</h1>

        <div class="callout hide" data-success="Invitation sent"></div>

        <table class="table-scroll">
            <tbody id="friend-rows">
            <?php wt_friend_row(0); ?>
            </tbody>
        </table>

        <script type="text/template" id="friend-template" data-append-to="friend-rows">
            <?php wt_friend_row(); ?>
        </script>

        <button type="button" class="small wt-clone wt-upper wt-text-secondary wt-margin-bottom-x2"
                data-template="friend-template">+ Invite more friends
        </button>

        <div class="text-center">
            <button type="submit" class="button secondary wt-upper" data-submit="Send Invite"
                    data-submitting="Sending..." data-submitted="Send Invite" data-enable="1">Send Invite
            </button>
        </div>
    </form>

<?php
if (empty($profile['ReferrerCode'])) {
    $response = wt_crm_api([
        'Command' => 'FRIENDS INVITATION',
        'EmailTemplateName' => 'FRIENDS INVITATION TEMPLATE',
        'MemberID' => $_SESSION['wt_crm']['MemberID'],
        'CardNo' => $_SESSION['wt_crm']['CardNo'],
        'NameList' => [],
        'EmailAddressLists' => [],
        "NoOfDigits" => 4,
        "SMSMaskName" => "Ascentis",
        "SendSMS" => "False",
        "MobileNoLists" => [""],
        "SMSMessage" => "",
        "SendEmail" => "False",
    ]);
//	wt_dump($response);

    if (!is_wp_error($response) && !empty($response['ReferrerCode']))
        $profile['ReferrerCode'] = $response['ReferrerCode'];
}

if (!empty($profile['ReferrerCode'])) {
    ?>

    <hr>

    <section>
        <h1 class="wt-h6 wt-upper wt-margin-bottom-x2">Share your invite</h1>
        <p>Share your referrer code below via your preferred social network or messaging apps. Your friend simply has to
            enter this code during sign up</p>

        <div class="row">
            <div class="columns medium-3">
                <div class="callout text-center">
                    <?php echo sanitize_text_field($profile['ReferrerCode']); ?>
                </div>
            </div>
        </div>
    </section>

    <?php
}
?>

<?php
$response = wt_crm_api([
    'Command' => 'GET FRIEND LISTS',
    'MemberID' => $_SESSION['wt_crm']['MemberID'],
    'RetrieveRefereeOnly' => true,

]);
//wt_dump($response);

if (!is_wp_error($response) && (!empty($response['TotalInvites']) || !empty($response['TotalNoOfFriends']))) {
    ?>

    <hr>

    <section>
        <h1 class="wt-h6 wt-upper wt-margin-bottom-x2">Referral Results</h1>

        <p class="wt-margin-bottom-x2">Total invites sent: <?php echo (int)$response['TotalInvites']; ?><br>
            Number of friends you have successfully referred: <?php echo (int)$response['TotalNoOfFriends']; ?></p>

        <?php
        if (!empty($response['FriendLists']) && is_array($response['FriendLists'])) {
            $friends = [];

            foreach ($response['FriendLists'] as $friend)
                $friends[$friend['JoinDate']] = $friend;

            ksort($friends);
            ?>

            <table class="wt-table">
                <thead>
                <tr class="wt-upper">
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($friends as $friend) { ?>
                    <tr>
                        <td><?php echo wt_crm_format_date($friend['JoinDate'], 'display'); ?></td>
                        <td><?php echo sanitize_text_field($friend['Friend_MemberName']); ?> has signed up! Here's 10
                            points for you.
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <?php
        }
        ?>
    </section>

    <?php
}