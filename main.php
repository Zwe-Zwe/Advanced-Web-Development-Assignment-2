<?php
include_once 'connection.php';

// Create user_table if not exists
$sql = "CREATE TABLE IF NOT EXISTS user_table (
    email VARCHAR(50) NOT NULL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dob DATE NULL,
    gender VARCHAR(6) NOT NULL,
    contact_number VARCHAR(15) NULL,
    hometown VARCHAR(50) NOT NULL,
    profile_image VARCHAR(100) NULL,
    student_id VARCHAR(50) NOT NULL UNIQUE -- Ensure unique student ID
)";
if ($conn->query($sql) !== TRUE) {
    echo "Error creating user_table: " . $conn->error;
}

// Create account_table if not exists
$sql = "CREATE TABLE IF NOT EXISTS account_table (
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    type VARCHAR(5) NOT NULL DEFAULT 'user', -- Default type set to 'user'
    FOREIGN KEY (email) REFERENCES user_table(email)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    PRIMARY KEY (email)
)";
if ($conn->query($sql) !== TRUE) {
    echo "Error creating account_table: " . $conn->error;
}

$sql = "CREATE TABLE IF NOT EXISTS plant_table (
    id INT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    scientific_name VARCHAR(50) NOT NULL UNIQUE, -- Ensure scientific name is unique
    common_name VARCHAR(50) NOT NULL,
    family VARCHAR(100) NOT NULL,
    genus VARCHAR(100) NOT NULL,
    species VARCHAR(100) NOT NULL,
    plants_image VARCHAR(255)NOT NULL,
    description VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'
)";
if ($conn->query($sql) !== TRUE) {
    echo "Error creating plant_table: " . $conn->error;
}




// Hash the passwords for insertion
$password = password_hash('Zwe@1224', PASSWORD_DEFAULT);
$password1 = password_hash('password1', PASSWORD_DEFAULT);
$password2 = password_hash('password2', PASSWORD_DEFAULT);
$password3 = password_hash('password3', PASSWORD_DEFAULT);
$password4 = password_hash('password4', PASSWORD_DEFAULT);
$admin_password = password_hash('admin', PASSWORD_DEFAULT);

// Insert dummy data into user_table (prevent duplicates with IGNORE)
$sql = "INSERT IGNORE INTO user_table (email, first_name, last_name, dob, gender, contact_number, hometown, profile_image, student_id)
VALUES 
    ('zwe@gmail.com', 'Zwe', 'Zaw', '1990-01-01', 'Male', '01153048122', 'Monywa', 'boys.jpg', 'ID000'),
    ('user1@example.com', 'John', 'Doe', '1990-01-01', 'Male', '01153048332', 'Hometown1', 'boys.jpg', 'ID001'),
    ('user2@example.com', 'Jane', 'Doe', '1991-02-01', 'Female', '01153048336', 'Hometown2', 'girl.png', 'ID002'),
    ('user3@example.com', 'Jim', 'Beam', '1992-03-01', 'Male', '01153048339', 'Hometown3', 'boys.jpg', 'ID003'),
    ('user4@example.com', 'Jack', 'Daniels', '1993-04-01', 'Male', '01153048338', 'Hometown4', 'boys.jpg', 'ID004'),
    ('admin@swin.edu.my', 'Admin', 'User', '1990-01-01', 'Male', '01153048337', 'HometownAdmin', 'boys.jpg', 'ID005')";
if ($conn->query($sql) !== TRUE) {
    echo "Error inserting data into user_table: " . $conn->error;
}

// Insert data into account_table with hashed passwords (prevent duplicates with IGNORE)
$sql = "INSERT IGNORE INTO account_table (email, password, type)
VALUES 
    ('zwe@gmail.com', '$password', 'user'),
    ('user1@example.com', '$password1', 'user'),
    ('user2@example.com', '$password2', 'user'),
    ('user3@example.com', '$password3', 'user'),
    ('user4@example.com', '$password4', 'user'),
    ('admin@swin.edu.my', '$admin_password', 'admin')";
if ($conn->query($sql) !== TRUE) {
    echo "Error inserting data into account_table: " . $conn->error;
}

$sql = "INSERT IGNORE INTO plant_table (scientific_name, common_name, family, genus, species, plants_image, description, status)
VALUES 
    ('Aegopodium podagraria', 'ground elder', 'Apiaceae', 'Aegopodium', 'Aegopodium podagraria', 'Aegopodium podagraria.jpg', 'Aegopodium podagraria.pdf', 'approved'),
    ('Alcea rosea', 'common hollyhock', 'Malvaceae', 'Alcea', 'Alcea rosea', 'Alcea rosea.jpg', 'Alcea rosea.pdf', 'approved'),
    ('Alliaria petiolata', 'garlic mustard', 'Brassicaceae', 'Alliaria', 'Alliaria petiolata', 'Alliaria petiolata.jpg', 'Alliaria petiolata.pdf', 'approved'),
    ('Anemone alpina', 'alpine anemone', 'Ranunculaceae', 'Anemone', 'Anemone alpina', 'Anemone alpina.jpg', 'Anemone alpina.pdf', 'approved'),
    ('Anemone hepatica', 'liverleaf', 'Ranunculaceae', 'Anemone', 'Anemone hepatica', 'Anemone hepatica.jpg', 'Anemone hepatica.pdf', 'approved'),
    ('Anemone hupehensis', 'Japanese anemone', 'Ranunculaceae', 'Anemone', 'Anemone hupehensis', 'Anemone hupehensis.jpg', 'Anemone hupehensis.pdf', 'approved'),
    ('Anemone nemorosa', 'wood anemone', 'Ranunculaceae', 'Anemone', 'Anemone nemorosa', 'Anemone nemorosa.jpg', NULL, 'approved'),
    ('Angelica sylvestris', 'wild angelica', 'Apiaceae', 'Angelica', 'Angelica sylvestris', 'Angelica sylvestris.jpg', 'Angelica sylvestris.pdf', 'approved'),
    ('Anthurium andraeanum', 'flamingo flower', 'Araceae', 'Anthurium', 'Anthurium andraeanum', 'Anthurium andraeanum.jpg', 'Anthurium andraeanum.pdf', 'approved'),
    ('Barbarea vulgaris', 'yellow rocket', 'Brassicaceae', 'Barbarea', 'Barbarea vulgaris', 'Barbarea vulgaris.jpg', 'Barbarea vulgaris.pdf', 'approved'),
    ('Calendula officinalis', 'pot marigold', 'Asteraceae', 'Calendula', 'Calendula officinalis', 'Calendula officinalis.jpg', NULL, 'approved'),
    ('Centranthus ruber', 'red valerian', 'Caprifoliaceae', 'Centranthus', 'Centranthus ruber', 'Centranthus ruber.jpg', 'Centranthus ruber.pdf', 'approved'),
    ('Cirsium arvense', 'creeping thistle', 'Asteraceae', 'Cirsium', 'Cirsium arvense', 'Cirsium arvense.jpg', 'Cirsium arvense.pdf', 'approved'),
    ('Cirsium vulgare', 'spear thistle', 'Asteraceae', 'Cirsium', 'Cirsium vulgare', 'Cirsium vulgare.jpg', 'Cirsium vulgare.pdf', 'approved'),
    ('Cucurbita pepo', 'zucchini', 'Cucurbitaceae', 'Cucurbita', 'Cucurbita pepo', 'Cucurbita pepo.jpg', NULL, 'approved'),
    ('Cymbalaria muralis', 'ivy-leaved toadflax', 'Plantaginaceae', 'Cymbalaria', 'Cymbalaria muralis', 'Cymbalaria muralis.jpg', NULL, 'approved'),
    ('Daucus carota', 'wild carrot', 'Apiaceae', 'Daucus', 'Daucus carota', 'Daucus carota.jpg', NULL, 'approved'),
    ('Fittonia albivenis', 'nerve plant', 'Acanthaceae', 'Fittonia', 'Fittonia albivenis', 'Fittonia albivenis.jpg', 'Fittonia albivenis.pdf', 'approved'),
    ('Fragaria vesca', 'wild strawberry', 'Rosaceae', 'Fragaria', 'Fragaria vesca', 'Fragaria vesca.jpg', NULL, 'approved'),
    ('Helminthotheca echioides', 'bristly oxtongue', 'Asteraceae', 'Helminthotheca', 'Helminthotheca echioides', 'Helminthotheca echioides.jpg', 'Helminthotheca echioides.pdf', 'approved'),
    ('Humulus lupulus', 'common hop', 'Cannabaceae', 'Humulus', 'Humulus lupulus', 'Humulus lupulus.jpg', NULL, 'approved'),
    ('Hypericum androsaemum', 'tutsan', 'Hypericaceae', 'Hypericum', 'Hypericum androsaemum', 'Hypericum androsaemum.jpg', NULL, 'approved'),
    ('Hypericum calycinum', 'Aaron’s beard', 'Hypericaceae', 'Hypericum', 'Hypericum calycinum', 'Hypericum calycinum.jpg', 'Hypericum calycinum.pdf', 'approved'),
    ('Hypericum perforatum', 'St John’s wort', 'Hypericaceae', 'Hypericum', 'Hypericum perforatum', 'Hypericum perforatum.jpg', NULL, 'approved'),
    ('Lactuca serriola', 'prickly lettuce', 'Asteraceae', 'Lactuca', 'Lactuca serriola', 'Lactuca serriola.jpg', 'Lactuca serriola.pdf', 'approved'),
    ('Lamium album', 'white dead-nettle', 'Lamiaceae', 'Lamium', 'Lamium album', 'Lamium album.jpg', NULL, 'approved'),
    ('Lamium galeobdolon', 'yellow archangel', 'Lamiaceae', 'Lamium', 'Lamium galeobdolon', 'Lamium galeobdolon.jpg', NULL, 'approved'),
    ('Lamium maculatum', 'spotted dead-nettle', 'Lamiaceae', 'Lamium', 'Lamium maculatum', 'Lamium maculatum.jpg', NULL, 'approved'),
    ('Lamium purpureum', 'red dead-nettle', 'Lamiaceae', 'Lamium', 'Lamium purpureum', 'Lamium purpureum.jpg', NULL, 'approved'),
    ('Lapsana communis', 'nipplewort', 'Asteraceae', 'Lapsana', 'Lapsana communis', 'Lapsana communis.jpg', NULL, 'approved'),
    ('Lavandula angustifolia', 'English lavender', 'Lamiaceae', 'Lavandula', 'Lavandula angustifolia', 'Lavandula angustifolia.jpg', NULL, 'approved'),
    ('Lavandula stoechas', 'Spanish lavender', 'Lamiaceae', 'Lavandula', 'Lavandula stoechas', 'Lavandula stoechas.jpg', NULL, 'approved'),
    ('Liriodendron tulipifera', 'tulip tree', 'Magnoliaceae', 'Liriodendron', 'Liriodendron tulipifera', 'Liriodendron tulipifera.jpg', NULL, 'approved'),
    ('Lupinus polyphyllus', 'garden lupin', 'Fabaceae', 'Lupinus', 'Lupinus polyphyllus', 'Lupinus polyphyllus.jpg', NULL, 'approved'),
    ('Melilotus albus', 'white sweet clover', 'Fabaceae', 'Melilotus', 'Melilotus albus', 'Melilotus albus.jpg', NULL, 'approved'),
    ('Mercurialis annua', 'annual mercury', 'Euphorbiaceae', 'Mercurialis', 'Mercurialis annua', 'Mercurialis annua.jpg', NULL, 'approved'),
    ('Ophrys apifera', 'bee orchid', 'Orchidaceae', 'Ophrys', 'Ophrys apifera', 'Ophrys apifera.jpg', NULL, 'approved'),
    ('Papaver rhoeas', 'corn poppy', 'Papaveraceae', 'Papaver', 'Papaver rhoeas', 'Papaver rhoeas.jpg', NULL, 'approved'),
    ('Papaver somniferum', 'opium poppy', 'Papaveraceae', 'Papaver', 'Papaver somniferum', 'Papaver somniferum.jpg', NULL, 'approved'),
    ('Pelargonium graveolens', 'rose geranium', 'Geraniaceae', 'Pelargonium', 'Pelargonium graveolens', 'Pelargonium graveolens.jpg', NULL, 'approved'),
    ('Pelargonium zonale', 'zonal geranium', 'Geraniaceae', 'Pelargonium', 'Pelargonium zonale', 'Pelargonium zonale.jpg', NULL, 'approved'),
    ('Perovskia atriplicifolia', 'Russian sage', 'Lamiaceae', 'Perovskia', 'Perovskia atriplicifolia', 'Perovskia atriplicifolia.jpg', NULL, 'approved'),
    ('Punica granatum', 'pomegranate', 'Lythraceae', 'Punica', 'Punica granatum', 'Punica granatum.jpg', NULL, 'approved'),
    ('Pyracantha coccinea', 'scarlet firethorn', 'Rosaceae', 'Pyracantha', 'Pyracantha coccinea', 'Pyracantha coccinea.jpg', NULL, 'approved'),
    ('Schefflera arboricola', 'dwarf umbrella tree', 'Araliaceae', 'Schefflera', 'Schefflera arboricola', 'Schefflera arboricola.jpg', NULL, 'approved'),
    ('Sedum acre', 'golden stonecrop', 'Crassulaceae', 'Sedum', 'Sedum acre', 'Sedum acre.jpg', NULL, 'approved'),
    ('Sedum album', 'white stonecrop', 'Crassulaceae', 'Sedum', 'Sedum album', 'Sedum album.jpg', NULL, 'approved'),
    ('Sedum rupestre', 'reflexed stonecrop', 'Crassulaceae', 'Sedum', 'Sedum rupestre', 'Sedum rupestre.jpg', NULL, 'approved'),
    ('Sedum sediforme', 'tassel stonecrop', 'Crassulaceae', 'Sedum', 'Sedum sediforme', 'Sedum sediforme.jpg', NULL, 'approved'),
    ('Smilax aspera', 'rough bindweed', 'Smilacaceae', 'Smilax', 'Smilax aspera', 'Smilax aspera.jpg', NULL, 'approved'),
    ('Tagetes erecta', 'African marigold', 'Asteraceae', 'Tagetes', 'Tagetes erecta', 'Tagetes erecta.jpg', NULL, 'approved'),
    ('Trachelospermum jasminoides', 'star jasmine', 'Apocynaceae', 'Trachelospermum', 'Trachelospermum jasminoides', 'Trachelospermum jasminoides.jpg', NULL, 'approved'),
    ('Tradescantia fluminensis', 'wandering jew', 'Commelinaceae', 'Tradescantia', 'Tradescantia fluminensis', 'Tradescantia fluminensis.jpg', NULL, 'approved'),
    ('Tradescantia pallida', 'purple heart', 'Commelinaceae', 'Tradescantia', 'Tradescantia pallida', 'Tradescantia pallida.jpg', NULL, 'approved'),
    ('Tradescantia virginiana', 'Virginia spiderwort', 'Commelinaceae', 'Tradescantia', 'Tradescantia virginiana', 'Tradescantia virginiana.jpg', NULL, 'approved'),
    ('Tradescantia zebrina', 'inch plant', 'Commelinaceae', 'Tradescantia', 'Tradescantia zebrina', 'Tradescantia zebrina.jpg', NULL, 'approved'),
    ('Trifolium incarnatum', 'crimson clover', 'Fabaceae', 'Trifolium', 'Trifolium incarnatum', 'Trifolium incarnatum.jpg', NULL, 'approved'),
    ('Trifolium pratense', 'red clover', 'Fabaceae', 'Trifolium', 'Trifolium pratense', 'Trifolium pratense.jpg', NULL, 'approved'),
    ('Trifolium repens', 'white clover', 'Fabaceae', 'Trifolium', 'Trifolium repens', 'Trifolium repens.jpg', NULL, 'approved'),
    ('Zamioculcas zamiifolia', 'ZZ plant', 'Araceae', 'Zamioculcas', 'Zamioculcas zamiifolia', 'Zamioculcas zamiifolia.jpg', NULL, 'approved')";
    
if ($conn->query($sql) !== TRUE) {
    echo "Error inserting data into plant_table: " . $conn->error;
}



$conn->close();
?>
