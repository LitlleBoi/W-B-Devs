<?php
header('Content-type: text/css');
include '../includes/connectie.php';
?>

*,
*::after,
*::before {
box-sizing: border-box;
}
.modal {
position: fixed;
top: 50%;
left: 50%;
transform: translate(-50%, 50%) scale(0);
transition: 200ms ease-in-out;
border: 1px solid black;
border-radius: 10px;
z-index: 10;
background-color: white;
width: 500px;
max-width: 80%;
}
.modal.active {
transform: translate(-50%, -50%) scale(1);
}
.modal-header {
padding: 10px 15px;
display: flex;
justify-content: space-between;
align-items: center;
border-bottom: 1px solid black;
}

.modal-header .title {
font-size: 1.25rem;
font-weight: bold;
}
.modal-header .close-button {
cursor: pointer;
border: none;
outline: none;
background: none;
font-size: 1.25rem;
font-weight: bold;
}
.modal-body {
padding: 10px 15px;
}
#overlay {
transition: 200ms ease-in-out;
position: fixed;
opacity: 0;
top: 0;
left: 0;
right: 0;
bottom: 0;
background-color: rgba(255, 255, 255, 0.5);
pointer-events: none;
}
#overlay.active {
opacity: 1;
pointer-events: auto;
}

<?php
foreach ($punten as $punt) {
    $id = $punt['id'];
    $x = $punt['x'];
    $y = $punt['y'];
    echo ".punt$id {\n";
    echo "  position: absolute;\n";
    echo "  left: {$x}px;\n";
    echo "  top: {$y}px;\n";
    echo "}\n";
}
?>