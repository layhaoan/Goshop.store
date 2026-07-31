<?php

session_start();

/* =========================================================
DATABASE CONNECTION
========================================================= */

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "store_db"
);

if($conn->connect_error){
    die("Connection Failed");
}

/* =========================================================
ONLY ADMIN CAN ACCESS
========================================================= */

if(
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != "Admin"
){

    die("
    <h2 style='
    color:red;
    font-family:Arial;
    text-align:center;
    margin-top:100px;
    '>

    Access Denied

    </h2>
    ");

}

/* =========================================================
RESET STAFF PERMISSION
========================================================= */

if(isset($_POST['reset_permission'])){

    $staff_id =
    $_POST['staff_id'];

    $new_role =
    $_POST['new_role'];

    /* UPDATE ROLE */

    $sql = "

    UPDATE staffs
    SET role='$new_role'
    WHERE id='$staff_id'

    ";

    if($conn->query($sql)){

        echo "

        <script>

        alert('Permission Updated Successfully');

        window.location='reset_permission.php';

        </script>

        ";

    }

}

/* =========================================================
GET STAFFS
========================================================= */

$get_staffs = "

SELECT *
FROM staff
ORDER BY id DESC

";

$staffs_result =
$conn->query($get_staff);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Reset Permission
</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial;
}

body{
    background:#f4f7fe;
    overflow-x:hidden;
}

/* =========================================================
SIDEBAR
========================================================= */

.sidebar{

    position:fixed;

    top:0;
    left:0;

    width:270px;
    height:100vh;

    background:
    linear-gradient(
        180deg,
        #071226,
        #0d1b36
    );

    padding:30px 20px;
}

/* LOGO */

.logo{
    color:white;
    font-size:38px;
    font-weight:800;
    margin-bottom:60px;
}

/* MENU */

.sidebar-menu{
    display:flex;
    flex-direction:column;
    gap:15px;
}

.sidebar-menu a{

    text-decoration:none;

    color:#dbe7ff;

    padding:16px 18px;

    border-radius:16px;

    display:flex;
    align-items:center;
    gap:14px;

    transition:.3s;
}

.sidebar-menu a:hover{
    background:rgba(255,255,255,.1);
}

.sidebar-menu a.active{

    background:
    linear-gradient(
        90deg,
        #2563ff,
        #3b82f6
    );

}

/* =========================================================
MAIN CONTENT
========================================================= */

.main-content{
    margin-left:270px;
    padding:35px;
}

/* =========================================================
TOPBAR
========================================================= */

.topbar{

    background:white;

    padding:20px 25px;

    border-radius:24px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:30px;

    box-shadow:
    0 10px 30px rgba(0,0,0,.05);
}

.topbar h1{
    color:#111827;
}

/* =========================================================
TABLE
========================================================= */

.permission-box{

    background:white;

    padding:30px;

    border-radius:28px;

    box-shadow:
    0 10px 30px rgba(0,0,0,.05);
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#071226;
    color:white;

    padding:18px;
    text-align:left;
}

td{
    padding:18px;
    border-bottom:1px solid #eee;
}

/* STAFF INFO */

.staff-info{
    display:flex;
    align-items:center;
    gap:14px;
}

.staff-info img{
    width:60px;
    height:60px;

    border-radius:50%;
    object-fit:cover;
}

/* ROLE BADGE */

.role-badge{

    display:inline-block;

    padding:8px 16px;

    border-radius:30px;

    font-size:14px;
}

.admin-role{
    background:#ffe7ef;
    color:#ff2d75;
}

.manager-role{
    background:#fff2df;
    color:#ff9800;
}

.staff-role{
    background:#eaf2ff;
    color:#2563ff;
}

/* SELECT */

select{

    padding:12px 14px;

    border:none;

    background:#f4f7fe;

    border-radius:12px;

    outline:none;
}

/* BUTTON */

.update-btn{

    padding:12px 18px;

    border:none;

    border-radius:12px;

    background:
    linear-gradient(
        135deg,
        #2563ff,
        #1d4ed8
    );

    color:white;

    cursor:pointer;

    transition:.3s;
}

.update-btn:hover{
    transform:translateY(-3px);
}

/* =========================================================
RESPONSIVE
========================================================= */

@media(max-width:900px){

    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }

    .main-content{
        margin-left:0;
    }

    table{
        display:block;
        overflow:auto;
    }

}

</style>

</head>

<body>

<!-- =========================================================
SIDEBAR
========================================================= -->

<div class="sidebar">

    <div class="logo">
        GOSHOP
    </div>

    <div class="sidebar-menu">

        <a href="Role_Permission_Dashboard.php">

            <i class="fa-solid fa-house"></i>

            Dashboard

        </a>

        <a href="staff_list.php">

            <i class="fa-solid fa-users"></i>

            Staff List

        </a>

        <a href="add_staff.php">

            <i class="fa-solid fa-user-plus"></i>

            Add Staff

        </a>

        <a
        href="reset_permission.php"
        class="active"
        >

            <i class="fa-solid fa-shield-halved"></i>

            Reset Permission

        </a>

    </div>

</div>

<!-- =========================================================
MAIN CONTENT
========================================================= -->

<div class="main-content">

    <!-- TOPBAR -->

    <div class="topbar">

        <h1>
            Reset Staff Permission
        </h1>

    </div>

    <!-- =====================================================
    PERMISSION TABLE
    ====================================================== -->

    <div class="permission-box">

        <table>

            <tr>

                <th>Staff</th>
                <th>Email</th>
                <th>Current Role</th>
                <th>Change Role</th>
                <th>Action</th>

            </tr>

            <?php

            if(
                $staffs_result &&
                $staffs_result->num_rows > 0
            ){

                while(
                    $staff =
                    $staffs_result->fetch_assoc()
                ){

                    $image =
                    "default.png";

                    if(
                        !empty($staff['image'])
                    ){
                        $image =
                        $staff['image'];
                    }

                    /* ROLE CLASS */

                    $role_class =
                    "staff-role";

                    if(
                        $staff['role'] == "Admin"
                    ){
                        $role_class =
                        "admin-role";
                    }

                    if(
                        $staff['role'] == "Manager"
                    ){
                        $role_class =
                        "manager-role";
                    }

            ?>

            <tr>

                <!-- STAFF -->

                <td>

                    <div class="staff-info">

                        <img

                        src="uploads/<?php echo $image; ?>"

                        onerror="this.src='uploads/default.png'"

                        >

                        <div>

                            <strong>

                                <?php
                                echo $staff['fullname'];
                                ?>

                            </strong>

                        </div>

                    </div>

                </td>

                <!-- EMAIL -->

                <td>

                    <?php
                    echo $staff['email'];
                    ?>

                </td>

                <!-- CURRENT ROLE -->

                <td>

                    <div class="role-badge <?php echo $role_class; ?>">

                        <?php
                        echo $staff['role'];
                        ?>

                    </div>

                </td>

                <!-- CHANGE ROLE -->

                <td>

                    <form method="POST">

                        <input
                        type="hidden"
                        name="staff_id"
                        value="<?php echo $staff['id']; ?>"
                        >

                        <select name="new_role">

                            <option value="Admin">
                                Admin
                            </option>

                            <option value="Manager">
                                Manager
                            </option>

                            <option value="Staff">
                                Staff
                            </option>

                        </select>

                </td>

                <!-- BUTTON -->

                <td>

                        <button

                        type="submit"

                        name="reset_permission"

                        class="update-btn"

                        >

                            Update

                        </button>

                    </form>

                </td>

            </tr>

            <?php

                }

            }

            ?>

        </table>

    </div>

</div>

</body>
</html>