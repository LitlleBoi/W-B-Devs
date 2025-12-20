-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 02:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `w_b_devs_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `bronnen`
--

CREATE TABLE `bronnen` (
  `id` int(11) NOT NULL,
  `punt_id` int(11) NOT NULL,
  `catalogusnummer` int(11) NOT NULL,
  `bron_type` enum('boek','artikel','website','video','document','ander') DEFAULT 'artikel',
  `titel` varchar(500) NOT NULL,
  `auteur` varchar(255) DEFAULT NULL,
  `publicatie_jaar` year(4) DEFAULT NULL,
  `bron-afbeelding` varchar(1000) DEFAULT NULL,
  `referentie_tekst` text DEFAULT NULL,
  `aangemaakt_op` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'concept'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bronnen`
--

INSERT INTO `bronnen` (`id`, `punt_id`, `catalogusnummer`, `bron_type`, `titel`, `auteur`, `publicatie_jaar`, `bron-afbeelding`, `referentie_tekst`, `aangemaakt_op`, `deleted_at`, `status`) VALUES
(4, 10, 76293, 'boek', 'Overheadschuitje Stadsbuitengracht', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/4.jpg', 'Afbeelding van het overheadschuitje over de Stadsbuitengracht ter hoogte van de Lange Smeestraat te Utrecht. Deze veerbootjes, die voetgangers van en naar de binnenstad vervoerden, werden in de loop van de 19e eeuw vervangen door vaste bruggen.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(5, 11, 24677, '', 'Stoombierbrouwerij De Krans', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/8.jpg', 'Gezicht op de stoombierbrouwerij De Krans (Nieuwekade 30) te Utrecht. Vanaf de middeleeuwen werd er in Utrecht volop bier gebrouwen. Tot ver in de 19e eeuw werd hier grachtenwater voor gebruikt.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(6, 12, 122380, 'boek', 'Catharijnebrug zuidwestelijk gezicht', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/69408f8e354b4_1765838734.jpg', 'Gezicht op de stadsbuitengracht en de Catharijnebrug te Utrecht, uit het zuidwesten, met op de achtergrond een gebouw waarin meerdere woonhuizen zijn gecombineerd aan de latere Catharijnekade. Omstreeks 1859.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(7, 12, 122379, 'boek', 'Catharijnebrug noordwestelijk gezicht', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/694081d83cfd4_1765835224.jpg', 'Gezicht op de Stadsbuitengracht en de Catharijnebrug te Utrecht, uit het noordwesten, met links het commiezenhuisje, daarachter de schoorsteen van de gasfabriek van W.H. de Heus op het Vredenburg.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(8, 12, 214852, 'document', 'Gasfabriek nieuwjaarswens 1857', 'Gaslantaarnopstekers', '0000', 'assets/images/aanvullend/11-1.jpg', 'Nieuwejaars Heil- en Zegenwensch van de gaslantaarnopstekers bij de aanvang van het jaar 1857 voor de gasfabriek van W.H. de Heus.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(9, 12, 122384, 'boek', 'Gasfabriek Vredenburg', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/694088de30dab_1765837022.jpg', 'Gezicht vanaf de Catharijnebrug op de stadsbuitengracht te Utrecht, uit het noordwesten, met links de gasfabriek van W.H. de Heus aan het Vredenburg. De steenmassa is het noordwestelijke bastion van het vroegere kasteel Vredenburg.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(10, 13, 216577, 'document', 'Plattegrond koperpletterij en gasfabriek', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/11-2.jpg', 'Plattegrond van het gebouwencomplex van de koperpletterij en gasfabriek van W.H. de Heus, gelegen tussen de Stadsbuitengracht en het Vredenburg te Utrecht; met vermelding van de bestemming van de gebouwen. Met legenda en een aantal doorhalingen en notities.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(11, 14, 30980, '', 'Willemsbrug zuidelijk gezicht', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/12-1.jpg', 'Gezicht vanaf de Catharijnesingel over de stadsbuitengracht te Utrecht met de Willemsbrug en enkele herenhuizen aan de Rijnkade en het Willemsplantsoen, uit het zuiden. Omstreeks 1850.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(12, 14, 30981, '', 'Willemsbrug zuidwestelijk gezicht', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/12-2.jpg', 'Gezicht vanaf de Catharijnesingel te Utrecht over de Willemsbrug met de beide commiezenhuisjes uit het zuidwesten, met links het hoekhuis aan de Rijnkade, rechts een herenhuis in het Willemsplantsoen en op de achtergrond de Mariaplaats en de Buur- en Domtoren. Omstreeks 1850.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(13, 15, 122421, '', 'Mariaplaats waterpomp', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/13.jpg', 'Gezicht op de Mariaplaats te Utrecht uit het westen. De pomp werd in 1844 op de Mariaplaats geplaatst en leverde schoon water, zelfs tijdens de cholera-uitbraken in de jaren 1870.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(14, 16, 212040, 'document', 'Zocher plantsoen plattegrond 1858', 'J.D. Zocher', '0000', 'assets/images/aanvullend/14.jpg', 'Plattegrond van de stad Utrecht met directe omgeving; met weergave van het stratenplan (deels met straatnamen), wegen en watergangen en aanduiding van de belangrijke gebouwen. Met weergave van alle groenvoorzieningen, waaronder de plantsoenen, door Zocher aangelegd op de geslechte wallen en bolwerken.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(15, 17, 122395, '', 'Meteorologisch Instituut Zonnenburg', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/23-1.jpg', 'Gezicht over de stadsbuitengracht te Utrecht op het Meteorologisch Instituut op het voormalige bastion Zonnenburg. Foto omstreeks 1859.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(16, 24, 122382, '', 'Sterrenwacht Zonnenburg', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/23-2.jpg', 'Gezicht over de stadsbuitengracht te Utrecht op de Sterrenwacht (Astronomisch Observatorium) op het voormalige bastion Zonnenburg. Foto omstreeks 1859.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(17, 18, 216219, 'document', 'Zocher ontwerp Lepelenburg', 'J.D. Zocher', '0000', 'assets/images/aanvullend/24.jpg', 'Plattegrond van een niet gevoerd ontwerp van Zocher voor een plantsoen op het bastion Lepelenburg te Utrecht.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(18, 19, 83821, '', 'Maliebrug noordoostelijk gezicht', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/26.jpg', 'Gezicht op de Maliebrug over de Stadsbuitengracht te Utrecht, uit het noordoosten.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(19, 20, 122389, '', 'Lucasbrug detail', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/31.jpg', 'De Lucasbrug werd ook wel \"knuppelbrug\" genoemd. De brug is opgebouwd uit schijnbaar willekeurig geplaatste ruwe boomstammetjes.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(20, 21, 35539, '', 'Suikerhuis voor afbraak', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/32.jpg', 'Gezicht op het Lucasbolwerk met het Suikerhuis te Utrecht, vóór de afbraak, uit het noorden. Het Suikerhuis was een suikerraffinaderij die in 1721 werd begonnen. In 1860 werd deze afgebroken.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(21, 13, 214852, '', 'Gasfabriek nieuwjaarswens 1857', 'Het Utrechts Archief', '0000', 'assets/images/aanvullend/11-1.jpg', 'Nieuwejaars Heil- en Zegenwensch van de gaslantaarnopstekers bij de aanvang van het jaar 1857 voor de gasfabriek van W.H. de Heus.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(22, 23, 0, '', 'De stadsbuitengracht', 'Het Utrechts Archief', '0000', '', 'De stadsbuitengracht (Singel)\r\nhad de taak als doorgaande scheepsroute overgenomen van de Oudegracht. Dit houtvlot\r\nbestaat uit aan elkaar gebonden rijen boomstammen. Zo’n transport was vaak dagenlang\r\nonderweg naar zijn eindbestemming, dikwijls Amsterdam.', '2025-11-25 10:37:24', NULL, 'gepubliceerd'),
(23, 26, 0, 'artikel', 'Badhuizen', 'Het Utrechts Archief', '0000', '', 'Badhuizen werden sinds eind 19e\r\neeuw gebouwd, toen er een grotere aandacht kwam voor hygiëne, gezondheid en levensstijl. De\r\nvraag naar hygiënische baden nam toe door industrialisatie en verstedelijking, wat leidde tot de\r\nbouw van openbare badhuizen, waar tegen betaling een bad of douche kon worden genomen.', '2025-11-25 10:37:24', NULL, 'gepubliceerd');

-- --------------------------------------------------------

--
-- Table structure for table `gebruikers`
--

CREATE TABLE `gebruikers` (
  `id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wachtwoord` varchar(255) NOT NULL,
  `rol` enum('admin','moderator') DEFAULT 'moderator',
  `aangemaakt_op` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gebruikers`
--

INSERT INTO `gebruikers` (`id`, `naam`, `email`, `wachtwoord`, `rol`, `aangemaakt_op`) VALUES
(1, 'Beheerder', 'admin@panorama.nl', '$2y$10$voorbeeldhash', 'admin', '2025-11-20 11:34:33'),
(2, 'Historisch Onderzoeker', 'onderzoeker@panorama.nl', '$2y$10$voorbeeldhash2', '', '2025-11-20 11:34:33'),
(3, 'Publieksdienst', 'publiek@panorama.nl', '$2y$10$voorbeeldhash3', '', '2025-11-20 11:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `panorama`
--

CREATE TABLE `panorama` (
  `id` int(11) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `afbeelding_url` varchar(500) NOT NULL,
  `beschrijving` text DEFAULT NULL,
  `catalogusnummer` int(11) DEFAULT NULL,
  `pagina` int(11) DEFAULT NULL,
  `gebruiker_id` int(11) NOT NULL,
  `status` enum('concept','gepubliceerd','gearchiveerd') DEFAULT 'concept',
  `aangemaakt_op` timestamp NOT NULL DEFAULT current_timestamp(),
  `bijgewerkt_op` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `auteursrechtlicentie` varchar(255) NOT NULL,
  `vervaardiger` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `panorama`
--

INSERT INTO `panorama` (`id`, `titel`, `afbeelding_url`, `beschrijving`, `catalogusnummer`, `pagina`, `gebruiker_id`, `status`, `aangemaakt_op`, `bijgewerkt_op`, `auteursrechtlicentie`, `vervaardiger`) VALUES
(1, 'PANORAMA VAN UTRECHT / vervaardigd in het Etablisement van / P.W. VAN DE WEIJER / Utrecht / WED. HERFKENS EN ZOON.', 'assets/images/1.jpg', 'Titelpagina van het Panorama van Utrecht, getekend door J. Bos, gedrukt bij P.W. van de Weijer en uitgegeven in juli 1859 door de Wed. Herfkens en zoon.', 135001, 1, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(2, 'WITTE - VROUWEN - BARRIERE.', 'assets/images/2.jpg', 'Gezicht over de Wittevrouwenbrug in de Wittevrouwenstraat te Utrecht met het douanekantoor (de latere politiepost Wittevrouwen) en de Willemskazerne.', 135002, 2, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(3, 'CELLULAIRE - GEVANGENIS.', 'assets/images/3.jpg', 'Gezicht op de gevangenis aan het Wolvenplein te Utrecht op het vroegere bolwerk Wolvenburg, met rechts een huis op de afgegraven stadswal bij de Wolvenstraat.', 135003, 3, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(4, 'PLOMPE-TOREN-GRACHT.\" en \"OVERHAALSCHUITJE - BEGIJNENHOF.', 'assets/images/4.jpg', 'Gezicht op de uitmonding van de Plompetorengracht in de stadsbuitengracht, met de Noorderkade en rechts een gedeelte van het Begijnebolwerk. Rechts wordt een overheadschuitje voortgetrokken.', 135004, 4, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(5, 'BEGIJNEN-BOLWERK.', 'assets/images/5.jpg', 'Gezicht op het Begijnebolwerk te Utrecht.', 135005, 5, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(6, 'VAN WIJKS-KADE.', 'assets/images/6.jpg', 'Gezicht op een gedeelte van het Begijnebolwerk (links) en de Van Asch van Wijckskade te Utrecht.', 135006, 6, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(7, 'WEERD-BARRIERE', 'assets/images/7.jpg', 'Gezicht op de Van Asch van Wijckskade, de Weerdbarrière en de Weerdbrug en rechts de Noorderkade met de stadswaag en stadskraan.', 135007, 7, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(8, 'NOORDER - KADE', 'assets/images/8.jpg', 'Gezicht op de Noorderkade te Utrecht, de Koninklijke Fabriek van Landbouwkundige Werktuigen, bierbrouwerij De Krans en het Paardenveld met de molen De Rijn en Zon.', 135008, 8, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(9, 'KOREN MOLEN DE MEIBOOM\"', 'assets/images/9.jpg', 'Gezicht op het Paardenveld te Utrecht met de molen De Meiboom en rechts een was- en badhuis, de latere Wasch- en Badinrichting van W. de Rijk.', 135009, 9, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(10, 'CATHARIJNE - BARRIERE', 'assets/images/10.jpg', 'Gezicht over de Catharijnebrug te Utrecht op een groot appartementengebouw, het douanekantoortje (de Catharijnebarrière), een herenhuis (later Bierhuis De Hoop) en de gasfabriek van W.H. de Heus.', 135010, 10, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-15 22:23:36', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(11, 'KOPERPLETTERIJ', 'assets/images/11.jpg', 'Gezicht op de koperpletterij van W.H. de Heus met het zuidwestelijke bastion van het vroegere kasteel Vredenburg en rechts de Rijnkade te Utrecht.', 135011, 11, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(12, 'WILLEMS - BARRIERE', 'assets/images/12.jpg', 'Gezicht over de Willemsbrug op de Rijnkade te Utrecht, het hek met de douanekantoortjes aan weerszijden van de brug (de Willemsbarrière) en rechts van de brug het begin van het singelplantsoen.', 135012, 12, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(13, 'GROOT \'S RIJKS HOSPITAAL', 'assets/images/13.jpg', 'Gezicht op het in Engelse landschapsstijl aangelegde singelplantsoen te Utrecht met het theehuis van de oud-rooms-katholieke aartsbisschop en rechts het hospital van het Duitse Huis.', 135013, 13, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(14, 'PLANTSOEN', 'assets/images/14.jpg', 'Gezicht op het singelplantsoen te Utrecht ter hoogte van de Zeven Steegjes. De opzet van het plan Zocher was om de minder aantrekkelijke delen van de stad te camoufleren.', 135014, 14, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(15, 'BARTHELOMEUS GASTHUIS', 'assets/images/15.jpg', 'Gezicht op het singelplantsoen te Utrecht met het Bartholomeusgasthuis.', 135015, 15, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(16, 'GEERTE - KERK.', 'assets/images/16.jpg', 'Gezicht op het singelplantsoen te Utrecht met links de Geertekerk en in de stadsbuitengracht een houtvlot.', 135016, 16, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(17, 'DIAKONESSEN HUIS', 'assets/images/17.jpg', 'Gezicht op het singelplantsoen te Utrecht met half achter de bomen het Diakonessenhuis aan de Springweg en rechts een gedeelte van het vroegere bastion Sterrenburg.', 135017, 17, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(18, 'RUN - EN - PELMOLEN', 'assets/images/18.jpg', 'Gezicht op het singelplantsoen te Utrecht met het dubbele woonhuis boven de kazematten van het vroegere bastion Sterrenburg en de molen op de Bijlhouwerstoren.', 135018, 18, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(19, 'TOLSTEEG - BARRIERE', 'assets/images/19.jpg', 'Gezicht over de Tolsteegbrug te Utrecht op de hekpalen van de Tolsteegbarrière bij het Ledig Erf met daaronder de uitmonding van de Oudegracht in de stadsbuitengracht.', 135019, 19, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(20, 'St. NICOLAI KERK', 'assets/images/20.jpg', 'Gezicht op het singelplantsoen te Utrecht met de zuidwestelijke toren van de Nicolaikerk en de cavaleriestallen met daarachter een gebouw van het voormalige St.Agnietenklooster.', 135020, 20, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(21, 'FUNDATIE - HUIS VAN VROUWE VAN RENSWOUDE', 'assets/images/21.jpg', 'Gezicht op het singelplantsoen te Utrecht met het gebouw van de Fundatie van de Vrijvrouwe van Renswoude en rechts de kameren van Maria van Pallaes aan de Agnietenstraat.', 135021, 21, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(22, 'NIEUWE - GRACHT- ONDER DE LINDEN', 'assets/images/22.jpg', 'Gezicht op het singelplantsoen te Utrecht met geheel links de regentenkamer van de kameren van Maria van Pallaes en daarnaast de Nieuwegracht \'Onder de Linden\'.', 135022, 22, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(23, 'METEOROLOGISCH INSTITUUT', 'assets/images/23.jpg', 'Gezicht op het singelplantsoen rond het voormalige bastion Zonnenburg te Utrecht met links een van de gebouwen van de voormalige St.-Servaasabdij, in het midden het Meteorologisch Instituut en rechts de Sterrenwacht.', 135023, 23, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(24, 'PLANTSOEN', 'assets/images/24.jpg', 'Gezicht op het singelplantsoen bij het Servaasbolwerkte Utrecht met rechts op de achtergrond een gedeelte van het St.-Magdalenaklooster.', 135024, 24, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(25, 'CHEMISCH LABORATORIUM', 'assets/images/25.jpg', 'Gezicht op het singelplantsoen bij het Servaasbolwerk te Utrecht met het gebouw van het voormalige Leeuwenberch gasthuis, destijds in gebruik als chemisch laboratorium.', 135025, 25, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(26, 'MALIE - BARRIERE', 'assets/images/26.jpg', 'Gezicht over de Maliebrug met het dubbele hek en het douanekantoortje (de Maliebarrière) te Utrecht op het singelplantsoen met geheel links een gedeelte van de Bruntenhof.', 135026, 26, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(27, 'BOLWERK - LEPELENBURG', 'assets/images/27.jpg', 'Gezicht op het voormalige bolwerk Lepelenburg te Utrecht met links het huis Lievendaal en rechts enkele particuliere tuinhuizen.', 135027, 27, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(28, 'BOLWERK - LEPELENBURG', 'assets/images/28.jpg', 'Gezicht op het voormalige bolwerk Lepelenburg te Utrecht met een aantal particuliere tuinen en tuinhuizen.', 135028, 28, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(29, 'HEEREN - STRAAT', 'assets/images/29.jpg', 'Gezicht op het singelplantsoen te Utrecht ten noorden van het voormalige bolwerk Lepelenburg, met in het midden de huizen aan het begin van de Herenstraat.', 135029, 29, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(30, 'KROMME - NIEUWE - GRACHT', 'assets/images/30.jpg', 'Gezicht op het singelplantsoen te Utrecht ter hoogte van de bocht van de Kromme Nieuwegracht met de huizen aan het Hieronymusplantsoen en daarachter de voormalige St.-Hieronymuskapel.', 135030, 30, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(31, 'St. LUCAS - BRUG', 'assets/images/31.jpg', 'Gezicht op het singelplantsoen te Utrecht met links de Zonstraat (later gewijzigd in Nobelstraat) die aansluit op de Lucasbrug, met rechts daarvan het Lucasbolwerk met het Suikerhuis.', 135031, 31, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(32, 'PLANTSOEN', 'assets/images/32.jpg', 'Gezicht op het singelplantsoen te Utrecht met links de noordelijke punt van het Lucasbolwerk met de directeurswoning van het Suikerhuis. Het Suikerhuis was een suikerraffinaderij die in 1721 werd begonnen.', 135032, 32, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus'),
(33, 'PLANTSOEN', 'assets/images/33.jpg', 'Gezicht op het singelplantsoen te Utrecht ten noorden van het Lucasbolwerk. Uiterst rechts sluit het plantsoen aan bij de Wittevrouwenbrug waarmee het panorama begint.', 135033, 33, 1, 'gepubliceerd', '2025-11-20 11:41:45', '2025-12-05 02:31:39', 'Publiek Domein 1.0', 'Bos, J., tekenaar/graficus');

-- --------------------------------------------------------

--
-- Stand-in structure for view `panorama_overzicht`
-- (See below for the actual view)
--
CREATE TABLE `panorama_overzicht` (
`id` int(11)
,`titel` varchar(255)
,`afbeelding_url` varchar(500)
,`catalogusnummer` int(11)
,`pagina` int(11)
,`status` enum('concept','gepubliceerd','gearchiveerd')
,`gebruiker_naam` varchar(255)
,`aangemaakt_op` timestamp
,`aantal_punten` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `punten`
--

CREATE TABLE `punten` (
  `id` int(11) NOT NULL,
  `panorama_id` int(11) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `omschrijving` text DEFAULT NULL,
  `x_coordinaat` int(11) NOT NULL,
  `y_coordinaat` int(11) NOT NULL,
  `gebruiker_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'concept',
  `aangemaakt_op` timestamp NOT NULL DEFAULT current_timestamp(),
  `bijgewerkt_op` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `punten`
--

INSERT INTO `punten` (`id`, `panorama_id`, `titel`, `omschrijving`, `x_coordinaat`, `y_coordinaat`, `gebruiker_id`, `status`, `aangemaakt_op`, `bijgewerkt_op`, `deleted_at`) VALUES
(10, 4, 'Overheadschuitje', 'Locatie waar het overheadschuitje wordt voortgetrokken', 1870, 776, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-15 06:52:08', NULL),
(11, 8, 'Brouwerij De Krans', 'Stoombierbrouwerij De Krans aan de Nieuwekade', 1207, 670, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:44:52', NULL),
(12, 10, 'Catharijnebrug en gasfabriek', 'Gezicht op de brug en gasfabriek van W.H. de Heus', 1359, 590, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-15 22:45:37', NULL),
(13, 11, 'Koperpletterij complex', 'Plattegrond van koperpletterij en gasfabriek', 390, 598, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:48:04', NULL),
(14, 12, 'Willemsbrug gezichten', 'Verschillende aanzichten van de Willemsbrug', 1278, 702, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:48:55', NULL),
(15, 13, 'Mariaplaats waterpomp', 'Waterpomp op Mariaplaats uit 1844', 405, 582, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:49:45', NULL),
(16, 14, 'Zocher plattegrond', 'Plattegrond Zocher plantsoenen 1858', 1040, 429, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:52:56', NULL),
(17, 23, 'Meteorologisch Instituut ', 'Wetenschappelijke instituten op Zonnenburg', 826, 487, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:57:57', NULL),
(18, 24, 'Zocher ontwerp Lepelenburg', 'Niet uitgevoerd ontwerp voor plantsoen', 1473, 540, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:59:36', NULL),
(19, 26, 'Maliebrug detail', 'Gedetailleerd aanzicht van de Maliebrug', 1665, 640, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 22:01:20', NULL),
(20, 31, 'Lucasbrug detail', 'Detailopname van de knuppelbrug', 820, 730, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 22:02:28', NULL),
(21, 32, 'Suikerhuis voor afbraak', 'Suikerraffinaderij voor sloop in 1860', 620, 540, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 22:03:11', NULL),
(23, 16, 'Het grote houtvlot', '', 1584, 720, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:54:52', NULL),
(24, 23, 'Sterrenwacht Zonnenburg', 'Wetenschappelijke instituten op Zonnenburg', 1900, 510, 1, 'gepubliceerd', '2025-11-25 10:37:24', '2025-12-08 21:58:35', NULL),
(25, 1, 'Titelblad', 'Het Panorama van Utrecht bestaat uit vier aaneengeplakte, zigzag gevouwen bladen\r\nmet een totale lengte van 5,82 meter. Het panorama is een meterslange tekening van een\r\nrondwandeling om het centrum van Utrecht, met steeds wisselend uitzicht vanaf de singels. Het\r\ngeeft een heel precies beeld van hoe de stad in 1859 er uitzag en het leuke is dat je ook het\r\nverloop van de seizoenen in de tekening terugziet.\r\n', 1050, 525, 1, 'gepubliceerd', '2025-12-04 20:30:10', '2025-12-08 21:41:59', NULL),
(26, 9, 'Badhuis', '', 1800, 598, 1, 'gepubliceerd', '2025-12-04 22:30:10', '2025-12-08 21:46:38', NULL);

-- --------------------------------------------------------

--
-- Structure for view `panorama_overzicht`
--
DROP TABLE IF EXISTS `panorama_overzicht`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `panorama_overzicht`  AS SELECT `p`.`id` AS `id`, `p`.`titel` AS `titel`, `p`.`afbeelding_url` AS `afbeelding_url`, `p`.`catalogusnummer` AS `catalogusnummer`, `p`.`pagina` AS `pagina`, `p`.`status` AS `status`, `g`.`naam` AS `gebruiker_naam`, `p`.`aangemaakt_op` AS `aangemaakt_op`, count(distinct `pt`.`id`) AS `aantal_punten` FROM ((`panorama` `p` left join `gebruikers` `g` on(`p`.`gebruiker_id` = `g`.`id`)) left join `punten` `pt` on(`p`.`id` = `pt`.`panorama_id`)) GROUP BY `p`.`id`, `p`.`titel`, `p`.`afbeelding_url`, `p`.`catalogusnummer`, `p`.`pagina`, `p`.`status`, `g`.`naam`, `p`.`aangemaakt_op` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bronnen`
--
ALTER TABLE `bronnen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_punt_id` (`punt_id`),
  ADD KEY `idx_bron_type` (`bron_type`),
  ADD KEY `idx_publicatie_jaar` (`publicatie_jaar`),
  ADD KEY `idx_bronnen_titel` (`titel`);

--
-- Indexes for table `gebruikers`
--
ALTER TABLE `gebruikers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_rol` (`rol`),
  ADD KEY `idx_gebruikers_naam` (`naam`);

--
-- Indexes for table `panorama`
--
ALTER TABLE `panorama`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_catalogusnummer` (`catalogusnummer`),
  ADD UNIQUE KEY `uk_pagina` (`pagina`),
  ADD KEY `idx_gebruiker_status` (`gebruiker_id`,`status`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_pagina` (`pagina`),
  ADD KEY `idx_panorama_titel` (`titel`);

--
-- Indexes for table `punten`
--
ALTER TABLE `punten`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_panorama_coordinaten` (`panorama_id`,`x_coordinaat`,`y_coordinaat`),
  ADD KEY `idx_gebruiker_id` (`gebruiker_id`),
  ADD KEY `idx_panorama_id` (`panorama_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_punten_titel` (`titel`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bronnen`
--
ALTER TABLE `bronnen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `punten`
--
ALTER TABLE `punten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `punten`
--
ALTER TABLE `punten`
  ADD CONSTRAINT `punten_ibfk_1` FOREIGN KEY (`panorama_id`) REFERENCES `panorama` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `punten_ibfk_2` FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
