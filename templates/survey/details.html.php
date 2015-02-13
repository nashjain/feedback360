<?php
$name = 'Survey';
if(count($data)>0) {
    $name = $data[0]['name'];
}
?>

<section class="wrapper style special fade">
    <div class="container">
        <h2><?php echo $name ?> Details</h2>
        <div class="table-wrapper">
            <table class="alt">
                <thead>
                <tr>
                    <th>Reviewee</th>
                    <th>Reviewer</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($data as $review) { ?>
                    <tr>
                        <td><a href="/user/<?php echo $review['reviewee'] ?>"><?php echo $review['reviewee'] ?></a></td>
                        <td><a href="/user/<?php echo $review['reviewer'] ?>"><?php echo $review['reviewer'] ?></a></td>
                        <td><?php echo $review['status'] ?></td>
                        <td><?php echo date( 'jS F Y h:i A', strtotime($review['updated'])) ?></td>
                        <td><a href="/review/<?php echo $review['id'] ?>">View</a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>