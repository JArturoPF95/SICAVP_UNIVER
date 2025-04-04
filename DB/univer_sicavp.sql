-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-04-2025 a las 11:57:50
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `univer_sicavp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academic_attendance`
--

CREATE TABLE `academic_attendance` (
  `AttendanceId` int(15) UNSIGNED ZEROFILL NOT NULL,
  `SCHEDULE_ID` int(11) NOT NULL,
  `ACADEMIC_ID` varchar(10) NOT NULL,
  `CODE_DAY` varchar(1) NOT NULL,
  `ATTENDANCE_DATE` date NOT NULL,
  `ATTENDANCE_TIME` time(6) NOT NULL,
  `SESSION` varchar(15) NOT NULL,
  `PROGRAM` varchar(6) NOT NULL,
  `CURRICULUM` varchar(6) NOT NULL,
  `GENERAL_ED` varchar(10) NOT NULL,
  `ROOM_ID` varchar(6) NOT NULL,
  `EVENT_ID` varchar(15) NOT NULL,
  `SECTION` varchar(4) NOT NULL,
  `SERIAL_ID` varchar(15) NOT NULL,
  `START_CLASS` time(6) NOT NULL,
  `END_CLASS` time(6) NOT NULL,
  `TINC` int(2) NOT NULL,
  `IN_OUT` int(1) NOT NULL,
  `CLASS_SUMMARY` varchar(255) NOT NULL,
  `JUSTIFY` varchar(1) DEFAULT NULL COMMENT 'P = Pendiente, S = Suplente, Y = Justificado, N = Rechazado\\r\\n\\r\\n',
  `COMMENT` varchar(255) DEFAULT NULL,
  `JUSTIFIED_BY` varchar(10) DEFAULT NULL,
  `JUSTIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Registra las asistencias de los docentes a sus clases';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academic_schedules`
--

CREATE TABLE `academic_schedules` (
  `NUM` int(6) NOT NULL,
  `PERSON_CODE_ID` varchar(10) DEFAULT NULL,
  `PREV_GOV_ID` varchar(20) DEFAULT NULL,
  `GOVERNMENT_ID` varchar(18) DEFAULT NULL,
  `LAST_NAME` varchar(60) DEFAULT NULL,
  `Last_Name_Prefix` varchar(60) DEFAULT NULL,
  `FIRST_NAME` varchar(60) DEFAULT NULL,
  `MIDDLE_NAME` varchar(60) DEFAULT NULL,
  `NAME` varchar(240) DEFAULT NULL,
  `ACADEMIC_YEAR` int(4) DEFAULT NULL,
  `ACADEMIC_TERM` varchar(10) DEFAULT NULL,
  `ACADEMIC_SESSION` varchar(10) DEFAULT NULL,
  `START_DATE` date DEFAULT NULL,
  `END_DATE` date DEFAULT NULL,
  `EVENT_ID` varchar(15) DEFAULT NULL,
  `PUBLICATION_NAME_1` varchar(100) DEFAULT NULL,
  `SECTION` varchar(4) DEFAULT NULL,
  `SERIAL_ID` varchar(30) DEFAULT NULL,
  `PROGRAM` varchar(6) DEFAULT NULL,
  `PROGRAM_DESC` varchar(40) DEFAULT NULL,
  `CURRICULUM` varchar(6) DEFAULT NULL,
  `FORMAL_TITLE` varchar(80) DEFAULT NULL,
  `CLASS_LEVEL` varchar(4) DEFAULT NULL,
  `CIP_CODE` varchar(6) DEFAULT NULL,
  `EVENT_STATUS` varchar(1) DEFAULT NULL,
  `GENERAL_ED` varchar(10) DEFAULT NULL,
  `DESC_GENERAL_ED` varchar(40) DEFAULT NULL,
  `ADDS` int(3) DEFAULT NULL,
  `BUILDING_CODE` varchar(6) DEFAULT NULL,
  `BUILD_NAME_1` varchar(45) DEFAULT NULL,
  `ROOM_ID` varchar(6) DEFAULT NULL,
  `ROOM_NAME` varchar(40) DEFAULT NULL,
  `DAY` varchar(4) DEFAULT NULL,
  `CODE_DAY` varchar(1) DEFAULT NULL,
  `MAX_BEFORE_CLASS` time NOT NULL,
  `START_CLASS` time NOT NULL,
  `DELAY_CLASS` time NOT NULL,
  `MAX_DELAY_CLASS` time NOT NULL,
  `MIN_END_CLASS` time NOT NULL,
  `END_CLASS` time NOT NULL,
  `SCHEDULED_MEETINGS` int(2) DEFAULT NULL,
  `PLANTILLA` varchar(2) DEFAULT NULL,
  `CONTACT_HR_SESSION` decimal(3,1) DEFAULT NULL,
  `FLAG_CLINIC` int(1) NOT NULL,
  `PK` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Almacena los horarios de los docentes obtenidos de las secciones de PwC';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academic_tolerance`
--

CREATE TABLE `academic_tolerance` (
  `CODE_PROGRAM` varchar(8) NOT NULL,
  `DESCRIPTION` varchar(65) NOT NULL,
  `MIN_TIME` int(2) NOT NULL,
  `DELAY_CLASS` int(2) NOT NULL,
  `MAX_CLASS` int(2) NOT NULL,
  `MIN_END` int(2) NOT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Se cargan las tolerancias de entrada - salida de clases docentes por Nivel - Programa. Consulta de PowerCampus, las tolerancias son parametrizadas en sistema SICAVP';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_attendance`
--

CREATE TABLE `admin_attendance` (
  `AttendanceId` int(11) UNSIGNED ZEROFILL NOT NULL,
  `NOM_ID` int(7) UNSIGNED ZEROFILL NOT NULL,
  `CODE_DAY` int(1) NOT NULL,
  `ATTENDANCE_DATE` date NOT NULL,
  `ATTENDANCE_TIME` time NOT NULL,
  `TINC` int(2) UNSIGNED ZEROFILL NOT NULL,
  `IN_OUT` int(11) NOT NULL,
  `BIO_SIC_FLAG` varchar(1) NOT NULL COMMENT 'Valor B = Captura de asistencia desde biotime. S = Captura de asistencia desde SICAVP',
  `COMMENTS` varchar(255) DEFAULT NULL,
  `JUSTIFY` varchar(1) DEFAULT NULL COMMENT 'Y = Justificado, N = Rechazado, P = Pendiente',
  `JUSTIFIED_BY` varchar(7) DEFAULT NULL,
  `JUSTIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Ragistra las asistencias del personal administrativo';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_schedules`
--

CREATE TABLE `admin_schedules` (
  `schedulesID` int(11) NOT NULL,
  `CODE_DAY` int(2) NOT NULL,
  `CODE_SCHEDULE` int(3) UNSIGNED ZEROFILL NOT NULL,
  `MIN_TIME_START` time(6) DEFAULT NULL,
  `TIME_START` time(6) DEFAULT NULL,
  `DELAY_TIME_START` time(6) DEFAULT NULL,
  `AUSENCE_TIME` time(6) DEFAULT NULL,
  `LOCK_IN_TIME` time(6) DEFAULT NULL,
  `MIN_TIME_OUT` time(6) DEFAULT NULL,
  `OUT_TIME` time(6) DEFAULT NULL,
  `CREATED_BY` varchar(10) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Tabla puente de horarios entrada, salida, tolerancias y días';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `assigned_schedule`
--

CREATE TABLE `assigned_schedule` (
  `ASSIGNMENT_ID` int(10) UNSIGNED NOT NULL,
  `ID_NOM` int(7) UNSIGNED ZEROFILL NOT NULL,
  `NAME` varchar(75) DEFAULT NULL,
  `YEAR` int(4) NOT NULL,
  `WEEK` int(2) NOT NULL,
  `START_DATE` date NOT NULL,
  `END_DATE` date NOT NULL,
  `SCHEDULE` int(3) UNSIGNED NOT NULL,
  `ASSIGNED_BY` varchar(10) DEFAULT NULL,
  `ASSIGNED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biometrictimeclock`
--

CREATE TABLE `biometrictimeclock` (
  `ID_NOM` int(7) UNSIGNED ZEROFILL DEFAULT NULL,
  `STATUS` varchar(30) DEFAULT NULL,
  `RECORD_DATE` date DEFAULT NULL,
  `RECORD_TIME` time DEFAULT NULL,
  `CREATED_BY` varchar(10) NOT NULL,
  `CREATED_DATE` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `PK` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendar`
--

CREATE TABLE `calendar` (
  `YEAR` int(4) NOT NULL,
  `CALENDAR_DATE` date NOT NULL,
  `CODE_DAY` int(1) NOT NULL,
  `DAY_OF_REST` int(1) NOT NULL,
  `DESCRIPTION` varchar(45) NOT NULL,
  `WEEK` int(2) NOT NULL,
  `PERIOD_PAYROLL_ID` int(11) DEFAULT NULL,
  `CREATED_BY` int(11) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` int(11) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Registra todas las fechas del año, y coloca una bandera en los días feriados, además del motivo del feriado (Independencia, Día del Trabajo, etc)';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_accesslevels`
--

CREATE TABLE `code_accesslevels` (
  `PAYROLL` int(5) UNSIGNED ZEROFILL NOT NULL,
  `CODE_LEVEL` int(1) NOT NULL,
  `LEVEL_DESCRIPTION` varchar(75) NOT NULL,
  `CREATED_BY` varchar(10) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Indica los niveles de acceso al sistema SICAVP';

--
-- Volcado de datos para la tabla `code_accesslevels`
--

INSERT INTO `code_accesslevels` (`PAYROLL`, `CODE_LEVEL`, `LEVEL_DESCRIPTION`, `CREATED_BY`, `CREATED_DATE`) VALUES
(00001, 1, 'EMPLEADO ADMINISTRATIVO', 'SOPADUNIV0', '0000-00-00 00:00:00'),
(00001, 2, 'SUPERVISOR ADMINISTRATIVO', 'SOPADUNIV0', '0000-00-00 00:00:00'),
(00001, 3, 'SOPORTE Y ADMINISTRACIÓN', 'SOPADUNIV0', '0000-00-00 00:00:00'),
(00003, 4, 'EMPLEADO DOCENTE', 'SOPADUNIV0', '0000-00-00 00:00:00'),
(00003, 5, 'SUPERVISOR DOCENTE', 'SOPADUNIV0', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_area`
--

CREATE TABLE `code_area` (
  `CODE_AREA` int(3) UNSIGNED ZEROFILL NOT NULL,
  `NAME_AREA` varchar(60) NOT NULL,
  `MODIFIED_BY` varchar(7) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Áreas, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_days`
--

CREATE TABLE `code_days` (
  `CODE_DAY` int(2) NOT NULL,
  `NAME_DAY` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de días de la semana';

--
-- Volcado de datos para la tabla `code_days`
--

INSERT INTO `code_days` (`CODE_DAY`, `NAME_DAY`) VALUES
(0, 'DOMINGO'),
(1, 'LUNES'),
(2, 'MARTES'),
(3, 'MIÉRCOLES'),
(4, 'JUEVES'),
(5, 'VIERNES'),
(6, 'SÁBADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_department`
--

CREATE TABLE `code_department` (
  `CODE_DEPRTMENT` int(3) UNSIGNED ZEROFILL NOT NULL,
  `DEPARTMENT` varchar(65) DEFAULT NULL,
  `CODE_AREA` int(3) UNSIGNED ZEROFILL NOT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Departamentos, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_incidence`
--

CREATE TABLE `code_incidence` (
  `CODE_TINC` int(2) UNSIGNED ZEROFILL NOT NULL,
  `DESCRIP_TINC` text NOT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Incidencias, se obtene de Nom2001';

--
-- Volcado de datos para la tabla `code_incidence`
--

INSERT INTO `code_incidence` (`CODE_TINC`, `DESCRIP_TINC`, `MODIFIED_BY`, `MODIFIED_DATE`) VALUES
(01, 'FALTA INJUSTIFICADA', NULL, NULL),
(02, 'FALTA POR RETARDOS', NULL, NULL),
(03, 'PERMISO SIN GOCE', NULL, NULL),
(04, 'SUSPENSION', NULL, NULL),
(05, 'PERMISO CON GOCE', NULL, NULL),
(21, 'ASISTENCIA', 'SOPADUNIV1', '2025-01-28 21:18:44'),
(22, 'RETARDO', 'SOPADUNIV1', '2025-01-28 21:18:44'),
(23, 'TIEMPO POR TIEMPO', 'SOPADUNIV1', '2025-01-28 21:18:44'),
(24, 'FERIADO', 'SOPADUNIV1', '2025-02-04 19:34:32'),
(25, 'VACACIONES', 'SOPADUNIV1', '2025-02-04 19:35:01'),
(26, 'SUPLENTE', 'SOPADUNIV1', '2025-03-04 22:07:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_institution`
--

CREATE TABLE `code_institution` (
  `CODE_INSTITUTION` int(3) UNSIGNED ZEROFILL NOT NULL,
  `INSTITUTION` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `CREATED_BY` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Institución, se obtene de Nom2001';

--
-- Volcado de datos para la tabla `code_institution`
--

INSERT INTO `code_institution` (`CODE_INSTITUTION`, `INSTITUTION`, `CREATED_BY`, `CREATED_DATE`) VALUES
(003, 'EDUCACION UNIVERSITARIA UNIVER', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_ip`
--

CREATE TABLE `code_ip` (
  `ipID` int(11) NOT NULL,
  `IP` varchar(18) NOT NULL,
  `IP_NAME` varchar(45) DEFAULT NULL,
  `CODE_SESION` int(5) UNSIGNED ZEROFILL NOT NULL,
  `CODE_CITY` int(3) UNSIGNED ZEROFILL NOT NULL,
  `CREATED_BY` varchar(10) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Contiene las direcciones IP y el campus/oficina al que pertenecen';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_jobs`
--

CREATE TABLE `code_jobs` (
  `CODE_JOB` int(6) UNSIGNED ZEROFILL NOT NULL,
  `JOB_NAME` varchar(60) NOT NULL,
  `CREATED_BY` varchar(7) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(7) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Puestos, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_month`
--

CREATE TABLE `code_month` (
  `CODE_MONTH` int(2) NOT NULL,
  `DESCRIPTION` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `code_month`
--

INSERT INTO `code_month` (`CODE_MONTH`, `DESCRIPTION`) VALUES
(1, 'Enero'),
(2, 'Febrero'),
(3, 'Marzo'),
(4, 'Abril'),
(5, 'Mayo'),
(6, 'Junio'),
(7, 'Julio'),
(8, 'Agosto'),
(9, 'Septiembre'),
(10, 'Octubre'),
(11, 'Noviembre'),
(12, 'Diciembre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_payroll`
--

CREATE TABLE `code_payroll` (
  `CODE_PAYROLL` int(5) UNSIGNED ZEROFILL NOT NULL,
  `DESCRIPTION` varchar(30) NOT NULL,
  `CREATED_BY` varchar(10) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Tipos de Nómina, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_position`
--

CREATE TABLE `code_position` (
  `CODE_POSITION` int(6) UNSIGNED ZEROFILL NOT NULL,
  `POSITION_DESCRIPTION` varchar(65) DEFAULT NULL,
  `CODE_JOB` int(6) UNSIGNED ZEROFILL NOT NULL,
  `CODE_NOM_SESSION` int(5) UNSIGNED ZEROFILL NOT NULL,
  `CODE_DEPARTMENT` int(3) UNSIGNED ZEROFILL NOT NULL,
  `CODE_AREA` int(3) UNSIGNED ZEROFILL NOT NULL,
  `BOSS_POSITION` int(6) UNSIGNED ZEROFILL NOT NULL,
  `CREATED_BY` varchar(10) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Posiciones, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_schedule`
--

CREATE TABLE `code_schedule` (
  `CODE_NOM` int(3) UNSIGNED ZEROFILL NOT NULL,
  `DAYTRIP` varchar(40) DEFAULT NULL,
  `SCHEDULE` varchar(200) DEFAULT NULL,
  `FLEX_SCHEDULE` int(1) NOT NULL,
  `CREATED_BY` varchar(10) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Jornadas, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_schedule_groups`
--

CREATE TABLE `code_schedule_groups` (
  `CODE` int(3) UNSIGNED ZEROFILL NOT NULL,
  `DESCRIPTION` varchar(40) NOT NULL,
  `FLEX_SCHEDULE` int(1) NOT NULL,
  `CREATED_BY` varchar(10) NOT NULL,
  `CREATED_DATE` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Grupos Horarios, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_sesion`
--

CREATE TABLE `code_sesion` (
  `CODE_SESION_NOM` int(5) UNSIGNED ZEROFILL NOT NULL,
  `NOM_SESION` varchar(20) NOT NULL,
  `CODE_COUNTRY` int(3) UNSIGNED ZEROFILL NOT NULL,
  `COUNTRY` varchar(25) NOT NULL,
  `CODE_CITY` int(3) UNSIGNED ZEROFILL NOT NULL,
  `CITY` varchar(50) NOT NULL,
  `CREATED_BY` varchar(10) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL,
  `PK` int(3) UNSIGNED ZEROFILL NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Campus, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_sesion_academic`
--

CREATE TABLE `code_sesion_academic` (
  `CODE_VALUE_KEY` varchar(15) NOT NULL,
  `CODE_VALUE` varchar(15) DEFAULT NULL,
  `SHORT_DESC` varchar(10) DEFAULT NULL,
  `MEDIUM_DESC` varchar(29) DEFAULT NULL,
  `LONG_DESC` varchar(19) DEFAULT NULL,
  `STATUS` varchar(3) DEFAULT NULL,
  `CREATE_DATE` varchar(10) DEFAULT NULL,
  `CREATE_OPID` varchar(3) DEFAULT NULL,
  `REVISION_DATE` varchar(10) DEFAULT NULL,
  `REVISION_OPID` varchar(3) DEFAULT NULL,
  `REVISION_TERMINAL` varchar(15) DEFAULT NULL,
  `CODE_XVAL` varchar(6) DEFAULT NULL,
  `CODE_XDESC` varchar(18) DEFAULT NULL,
  `ABT_JOIN` varchar(1) DEFAULT NULL,
  `SORT_ORDER` int(2) DEFAULT NULL,
  `SessionId` int(1) DEFAULT NULL,
  `CODE_NOM` int(3) UNSIGNED ZEROFILL NOT NULL,
  `CREATE_TIME` varchar(10) NOT NULL,
  `REVISION_TIME` varchar(10) NOT NULL,
  `CREATE_TERMINAL` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Campus para Docentes, se obtene de PowerCampus, y se enlaza con el de Nom2001 por clave, que se actualiza en sistema SICAVP';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `code_vacation`
--

CREATE TABLE `code_vacation` (
  `vacID` int(11) NOT NULL,
  `MIN_YEARS` decimal(5,2) NOT NULL,
  `MAX_YEARS` decimal(5,2) NOT NULL,
  `DAYS_BY_LAW` int(3) NOT NULL,
  `PREV_DAYS` int(2) NOT NULL COMMENT 'Días mínimos requeridos para solicitar vacaciones',
  `CREATED_BY` varchar(7) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Contiene los días de vacaciones a los que tiene derecho el colaborador administrativo por antigüedad';

--
-- Volcado de datos para la tabla `code_vacation`
--

INSERT INTO `code_vacation` (`vacID`, `MIN_YEARS`, `MAX_YEARS`, `DAYS_BY_LAW`, `PREV_DAYS`, `CREATED_BY`, `CREATED_DATE`, `MODIFIED_BY`, `MODIFIED_DATE`) VALUES
(1, 1.00, 1.99, 12, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(2, 2.00, 2.99, 14, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(3, 3.00, 3.99, 16, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(4, 4.00, 4.99, 18, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(5, 5.00, 5.99, 20, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(6, 6.00, 10.99, 22, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(7, 11.00, 15.99, 24, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(8, 16.00, 20.99, 26, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(9, 21.00, 25.99, 28, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(10, 26.00, 30.99, 30, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55'),
(11, 31.00, 100.00, 32, 10, 'SOPADUN', '2024-04-11 00:38:55', 'SOPADUNIV0', '2024-04-11 00:38:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_employed`
--

CREATE TABLE `email_employed` (
  `DOMAIN` varchar(65) NOT NULL,
  `ADD_BY` varchar(10) DEFAULT NULL,
  `ADD_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Almacena los correos electrónicos institucionales';

--
-- Volcado de datos para la tabla `email_employed`
--

INSERT INTO `email_employed` (`DOMAIN`, `ADD_BY`, `ADD_DATE`, `MODIFIED_BY`, `MODIFIED_DATE`) VALUES
('docente.univer-gdl.edu.mx', 'SOPADUNIV1', '2025-01-24 23:17:04', 'SOPADUNIV1', '2025-01-24 23:17:04'),
('nacerglobal.com.mx', 'SOPADUNIV1', '2025-01-24 23:18:59', 'SOPADUNIV1', '2025-01-24 23:18:59'),
('univer-gdl.edu.mx', 'SOPADUNIV1', '2025-01-24 23:17:04', 'SOPADUNIV1', '2025-01-24 23:17:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employed`
--

CREATE TABLE `employed` (
  `INSTITUTION` int(3) UNSIGNED ZEROFILL NOT NULL,
  `ID_NOM` int(7) UNSIGNED ZEROFILL NOT NULL,
  `PAYROLL` int(5) UNSIGNED ZEROFILL DEFAULT NULL,
  `LAST_NAME` varchar(25) DEFAULT NULL,
  `LAST_NAME_PREFIX` varchar(25) DEFAULT NULL,
  `NAME` varchar(40) DEFAULT NULL,
  `TAXPAYER_ID` varchar(13) NOT NULL,
  `GOVERNMENT_ID` varchar(18) NOT NULL,
  `IMSS` bigint(11) UNSIGNED ZEROFILL NOT NULL,
  `STATUS` varchar(1) DEFAULT NULL,
  `AREA` int(3) UNSIGNED ZEROFILL NOT NULL,
  `DEPARTMENT` int(3) UNSIGNED ZEROFILL NOT NULL,
  `COUNTRY` int(3) UNSIGNED ZEROFILL NOT NULL,
  `CITY` int(3) UNSIGNED ZEROFILL NOT NULL,
  `NOM_SESSION` int(5) UNSIGNED ZEROFILL NOT NULL,
  `JOB` int(6) UNSIGNED ZEROFILL NOT NULL,
  `POSITION` int(6) UNSIGNED ZEROFILL NOT NULL,
  `SCHEDULE_GROUP` int(3) UNSIGNED ZEROFILL NOT NULL,
  `DAYTRIP` int(3) UNSIGNED ZEROFILL NOT NULL,
  `ADMISSION_DATE` date NOT NULL,
  `PERMANENT_EMP_DATE` date NOT NULL,
  `ANTIQUITY` date NOT NULL,
  `CLASIF` varchar(3) DEFAULT NULL,
  `SEPARATION_DATE` date DEFAULT NULL,
  `SEPARATION_COMMENTS` varchar(150) DEFAULT NULL,
  `CONTRACT` varchar(2) NOT NULL,
  `CONTRACT_START` date NOT NULL,
  `CONTRACT_END` date DEFAULT NULL,
  `GENRE` varchar(1) NOT NULL,
  `POSITION_SUEPRVISOR` int(6) UNSIGNED ZEROFILL DEFAULT NULL,
  `SUPERVISOR_ID` int(7) UNSIGNED ZEROFILL DEFAULT NULL,
  `SUPERVISOR_NAME` varchar(90) DEFAULT NULL,
  `SUPERVISOR_ID_AUX` int(7) UNSIGNED ZEROFILL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Información de los empleados. Se obtiene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employed_backup`
--

CREATE TABLE `employed_backup` (
  `INSTITUTION` int(3) UNSIGNED ZEROFILL NOT NULL,
  `ID_NOM` int(7) UNSIGNED ZEROFILL NOT NULL,
  `PAYROLL` int(5) UNSIGNED ZEROFILL DEFAULT NULL,
  `LAST_NAME` varchar(25) DEFAULT NULL,
  `LAST_NAME_PREFIX` varchar(25) DEFAULT NULL,
  `NAME` varchar(40) DEFAULT NULL,
  `TAXPAYER_ID` varchar(13) NOT NULL,
  `GOVERNMENT_ID` varchar(18) NOT NULL,
  `IMSS` bigint(11) UNSIGNED ZEROFILL NOT NULL,
  `STATUS` varchar(1) DEFAULT NULL,
  `AREA` int(3) UNSIGNED ZEROFILL NOT NULL,
  `DEPARTMENT` int(3) UNSIGNED ZEROFILL NOT NULL,
  `COUNTRY` int(3) UNSIGNED ZEROFILL NOT NULL,
  `CITY` int(3) UNSIGNED ZEROFILL NOT NULL,
  `NOM_SESSION` int(5) UNSIGNED ZEROFILL NOT NULL,
  `JOB` int(6) UNSIGNED ZEROFILL NOT NULL,
  `POSITION` int(6) UNSIGNED ZEROFILL NOT NULL,
  `SCHEDULE_GROUP` int(3) UNSIGNED ZEROFILL NOT NULL,
  `DAYTRIP` int(3) UNSIGNED ZEROFILL NOT NULL,
  `ADMISSION_DATE` date NOT NULL,
  `PERMANENT_EMP_DATE` date NOT NULL,
  `ANTIQUITY` date NOT NULL,
  `CLASIF` varchar(3) DEFAULT NULL,
  `SEPARATION_DATE` date NOT NULL,
  `SEPARATION_COMMENTS` varchar(150) DEFAULT NULL,
  `CONTRACT` varchar(2) NOT NULL,
  `CONTRACT_START` date NOT NULL,
  `CONTRACT_END` date NOT NULL,
  `GENRE` varchar(1) NOT NULL,
  `POSITION_SUEPRVISOR` int(6) UNSIGNED ZEROFILL NOT NULL,
  `SUPERVISOR_ID` int(7) UNSIGNED ZEROFILL NOT NULL,
  `SUPERVISOR_NAME` varchar(90) NOT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL,
  `BACKUP_BY` varchar(10) DEFAULT NULL,
  `BACKUP_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='BackUps de  Información de los empleados. Se obtiene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups_daytrip`
--

CREATE TABLE `groups_daytrip` (
  `CODE_GROUP` int(3) UNSIGNED ZEROFILL NOT NULL,
  `GROUP_NAME` varchar(40) NOT NULL,
  `CODE_DAYTRIP` int(3) UNSIGNED ZEROFILL NOT NULL,
  `DAYTRIP` varchar(40) NOT NULL,
  `ROLE_ORDER` int(3) NOT NULL,
  `FLEX_SCHEDULE` int(1) NOT NULL,
  `CREATED_BY` varchar(7) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `PK` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Grupo Jornada, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mapping_bioadminemploy`
--

CREATE TABLE `mapping_bioadminemploy` (
  `ID_BIOUNIVER` varchar(12) NOT NULL,
  `NAME_NOM2001` varchar(75) NOT NULL,
  `ID_NOM2001` int(7) UNSIGNED ZEROFILL NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payroll_period`
--

CREATE TABLE `payroll_period` (
  `INSTITUTION` int(3) UNSIGNED ZEROFILL NOT NULL,
  `PAYROLL` int(5) UNSIGNED ZEROFILL NOT NULL,
  `CODE` varchar(12) NOT NULL,
  `DESCRIPTION` varchar(40) NOT NULL,
  `START_DATE` date NOT NULL,
  `END_DATE` date NOT NULL,
  `STATUS` varchar(7) DEFAULT NULL,
  `YEAR` int(4) DEFAULT NULL,
  `MONTH` int(4) DEFAULT NULL,
  `BIMESTER` int(1) DEFAULT NULL,
  `ACUM_MONTH` int(1) DEFAULT NULL,
  `TERM` int(1) DEFAULT NULL,
  `PAY_REAL_DATE` date NOT NULL,
  `ACUM_DATE` date DEFAULT NULL,
  `TERM_CLOSE` varchar(10) DEFAULT NULL,
  `TYPE` varchar(20) DEFAULT NULL,
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Catálogo de Periodos de Nómina, se obtene de Nom2001';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `SICAVP_USER` varchar(10) NOT NULL,
  `PASS_TEMP` varchar(10) NOT NULL,
  `PASSWORD` varchar(80) NOT NULL,
  `NOM_SESSION` int(5) UNSIGNED ZEROFILL NOT NULL,
  `CITY` int(3) UNSIGNED ZEROFILL NOT NULL,
  `PAYROLL` int(5) UNSIGNED ZEROFILL NOT NULL,
  `ACCESS_LEVEL` int(2) NOT NULL,
  `SEPARATION_FLAG` int(1) NOT NULL,
  `EMAIL` varchar(55) DEFAULT NULL,
  `FLAG_CONFIRM` int(1) NOT NULL,
  `CREATED_BY` varchar(10) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Almacena los usuarios y sus accesios al sistema';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_last_access`
--

CREATE TABLE `users_last_access` (
  `USER` varchar(10) NOT NULL,
  `NOM_SESSION` int(5) UNSIGNED ZEROFILL NOT NULL,
  `PAYROLL` int(1) NOT NULL,
  `ACCESS_LEVEL` int(11) NOT NULL,
  `SEPARATION_FLAG` int(1) NOT NULL,
  `LAST_ACCESS` timestamp NULL DEFAULT NULL,
  `UBICATION` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Tabla espejo de usuarios. Se indica desde dónde y a qué hora ingresó al sistema por última vez';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacation_anticipated`
--

CREATE TABLE `vacation_anticipated` (
  `PK` int(11) NOT NULL,
  `ID_NOM` varchar(7) NOT NULL,
  `VACATION_TERM` varchar(4) NOT NULL,
  `DAYS` int(2) NOT NULL,
  `COMMENTS` varchar(255) NOT NULL,
  `ACTIVE_FLAG` varchar(1) NOT NULL,
  `REQUEST_FLAG` int(1) DEFAULT NULL,
  `CREATED_BY` varchar(10) NOT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL,
  `MODIFIED_BY` varchar(10) DEFAULT NULL,
  `MODIFIED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacation_request`
--

CREATE TABLE `vacation_request` (
  `requestId` int(11) UNSIGNED ZEROFILL NOT NULL,
  `ID_NOM` varchar(7) NOT NULL,
  `REQUEST_TERM` int(4) NOT NULL,
  `REQUEST_DATE` date NOT NULL,
  `START_DATE` date NOT NULL,
  `END_DATE` date NOT NULL,
  `DAYS_REQUESTED` int(3) NOT NULL,
  `AUTHORIZATION_FLAG` int(2) NOT NULL COMMENT '0 = Pendiente, 2 = Rechazada, 1 = Autorizada',
  `IMMEDIATE_BOSS` varchar(7) NOT NULL,
  `COMMENTS` varchar(255) DEFAULT NULL,
  `AUTHORIZED_BY` varchar(7) DEFAULT NULL,
  `AUTHORIZED_DATE` timestamp NULL DEFAULT NULL,
  `CREATED_BY` varchar(7) DEFAULT NULL,
  `CREATED_DATE` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Registra las solicitudes de vacaciones de los colaboradores';

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `academic_attendance`
--
ALTER TABLE `academic_attendance`
  ADD PRIMARY KEY (`AttendanceId`),
  ADD KEY `INDEX_ACAATTENDANCE` (`SCHEDULE_ID`,`ACADEMIC_ID`,`CODE_DAY`,`SESSION`,`TINC`,`ATTENDANCE_DATE`) USING BTREE;

--
-- Indices de la tabla `academic_schedules`
--
ALTER TABLE `academic_schedules`
  ADD PRIMARY KEY (`PK`),
  ADD KEY `ACADEMIC_YEAR` (`ACADEMIC_YEAR`,`ACADEMIC_TERM`,`ACADEMIC_SESSION`,`PROGRAM`,`CURRICULUM`,`GENERAL_ED`,`CODE_DAY`);

--
-- Indices de la tabla `academic_tolerance`
--
ALTER TABLE `academic_tolerance`
  ADD PRIMARY KEY (`CODE_PROGRAM`);

--
-- Indices de la tabla `admin_attendance`
--
ALTER TABLE `admin_attendance`
  ADD PRIMARY KEY (`AttendanceId`),
  ADD UNIQUE KEY `UNIQUE_ATTENDANCE` (`NOM_ID`,`ATTENDANCE_DATE`,`IN_OUT`) USING BTREE,
  ADD KEY `INDEX_ATTENDANCE` (`NOM_ID`,`CODE_DAY`,`TINC`,`ATTENDANCE_DATE`) USING BTREE;

--
-- Indices de la tabla `admin_schedules`
--
ALTER TABLE `admin_schedules`
  ADD PRIMARY KEY (`schedulesID`),
  ADD KEY `ID_NOM` (`CODE_DAY`,`CODE_SCHEDULE`);

--
-- Indices de la tabla `assigned_schedule`
--
ALTER TABLE `assigned_schedule`
  ADD PRIMARY KEY (`ASSIGNMENT_ID`),
  ADD UNIQUE KEY `ID_NOM_2` (`ID_NOM`,`YEAR`,`WEEK`) USING BTREE,
  ADD KEY `ID_NOM` (`ID_NOM`,`WEEK`,`SCHEDULE`,`YEAR`) USING BTREE;

--
-- Indices de la tabla `biometrictimeclock`
--
ALTER TABLE `biometrictimeclock`
  ADD PRIMARY KEY (`PK`),
  ADD KEY `CVE_CHECADOR` (`ID_NOM`);

--
-- Indices de la tabla `calendar`
--
ALTER TABLE `calendar`
  ADD PRIMARY KEY (`CALENDAR_DATE`),
  ADD KEY `CODE_DAY` (`CODE_DAY`,`YEAR`) USING BTREE;

--
-- Indices de la tabla `code_accesslevels`
--
ALTER TABLE `code_accesslevels`
  ADD PRIMARY KEY (`CODE_LEVEL`) USING BTREE,
  ADD KEY `PAYROLL` (`PAYROLL`);

--
-- Indices de la tabla `code_area`
--
ALTER TABLE `code_area`
  ADD PRIMARY KEY (`CODE_AREA`),
  ADD KEY `CODE_AREA` (`CODE_AREA`);

--
-- Indices de la tabla `code_days`
--
ALTER TABLE `code_days`
  ADD PRIMARY KEY (`CODE_DAY`);

--
-- Indices de la tabla `code_department`
--
ALTER TABLE `code_department`
  ADD PRIMARY KEY (`CODE_DEPRTMENT`,`CODE_AREA`),
  ADD KEY `CODE_AREA` (`CODE_AREA`);

--
-- Indices de la tabla `code_incidence`
--
ALTER TABLE `code_incidence`
  ADD PRIMARY KEY (`CODE_TINC`);

--
-- Indices de la tabla `code_institution`
--
ALTER TABLE `code_institution`
  ADD PRIMARY KEY (`CODE_INSTITUTION`);

--
-- Indices de la tabla `code_ip`
--
ALTER TABLE `code_ip`
  ADD PRIMARY KEY (`ipID`),
  ADD KEY `CODE_SESION` (`CODE_SESION`,`CODE_CITY`);

--
-- Indices de la tabla `code_jobs`
--
ALTER TABLE `code_jobs`
  ADD PRIMARY KEY (`CODE_JOB`);

--
-- Indices de la tabla `code_month`
--
ALTER TABLE `code_month`
  ADD PRIMARY KEY (`CODE_MONTH`);

--
-- Indices de la tabla `code_payroll`
--
ALTER TABLE `code_payroll`
  ADD PRIMARY KEY (`CODE_PAYROLL`);

--
-- Indices de la tabla `code_position`
--
ALTER TABLE `code_position`
  ADD PRIMARY KEY (`CODE_POSITION`),
  ADD KEY `CODE_JOB` (`CODE_JOB`,`CODE_NOM_SESSION`,`CODE_DEPARTMENT`,`CODE_AREA`,`BOSS_POSITION`);

--
-- Indices de la tabla `code_schedule`
--
ALTER TABLE `code_schedule`
  ADD PRIMARY KEY (`CODE_NOM`);

--
-- Indices de la tabla `code_schedule_groups`
--
ALTER TABLE `code_schedule_groups`
  ADD PRIMARY KEY (`CODE`,`FLEX_SCHEDULE`) USING BTREE;

--
-- Indices de la tabla `code_sesion`
--
ALTER TABLE `code_sesion`
  ADD PRIMARY KEY (`PK`),
  ADD KEY `CODE_SESION_NOM` (`CODE_SESION_NOM`,`CODE_COUNTRY`,`CODE_CITY`);

--
-- Indices de la tabla `code_sesion_academic`
--
ALTER TABLE `code_sesion_academic`
  ADD PRIMARY KEY (`CODE_VALUE_KEY`),
  ADD KEY `CODE_VALUE_KEY` (`CODE_VALUE_KEY`,`LONG_DESC`,`CODE_NOM`);

--
-- Indices de la tabla `code_vacation`
--
ALTER TABLE `code_vacation`
  ADD PRIMARY KEY (`vacID`),
  ADD KEY `MIN_AGNOS` (`MIN_YEARS`,`MAX_YEARS`);

--
-- Indices de la tabla `email_employed`
--
ALTER TABLE `email_employed`
  ADD PRIMARY KEY (`DOMAIN`);

--
-- Indices de la tabla `employed`
--
ALTER TABLE `employed`
  ADD PRIMARY KEY (`ID_NOM`),
  ADD KEY `INDEX_EMPLOYED` (`INSTITUTION`,`PAYROLL`,`AREA`,`DEPARTMENT`,`NOM_SESSION`,`JOB`,`POSITION`,`SCHEDULE_GROUP`,`DAYTRIP`) USING BTREE;

--
-- Indices de la tabla `employed_backup`
--
ALTER TABLE `employed_backup`
  ADD KEY `INDEX_EMPLOYED` (`INSTITUTION`,`PAYROLL`,`AREA`,`DEPARTMENT`,`NOM_SESSION`,`JOB`,`POSITION`,`SCHEDULE_GROUP`,`DAYTRIP`) USING BTREE;

--
-- Indices de la tabla `groups_daytrip`
--
ALTER TABLE `groups_daytrip`
  ADD PRIMARY KEY (`PK`),
  ADD KEY `CODE_DAYTRIP` (`CODE_DAYTRIP`,`ROLE_ORDER`);

--
-- Indices de la tabla `mapping_bioadminemploy`
--
ALTER TABLE `mapping_bioadminemploy`
  ADD PRIMARY KEY (`ID_NOM2001`);

--
-- Indices de la tabla `payroll_period`
--
ALTER TABLE `payroll_period`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `INSTITUTION` (`INSTITUTION`,`PAYROLL`,`YEAR`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`SICAVP_USER`),
  ADD KEY `NOM_SESSION` (`NOM_SESSION`,`CITY`,`PAYROLL`,`ACCESS_LEVEL`);

--
-- Indices de la tabla `users_last_access`
--
ALTER TABLE `users_last_access`
  ADD PRIMARY KEY (`USER`),
  ADD KEY `NOM_SESSION` (`NOM_SESSION`,`PAYROLL`,`ACCESS_LEVEL`);

--
-- Indices de la tabla `vacation_anticipated`
--
ALTER TABLE `vacation_anticipated`
  ADD PRIMARY KEY (`PK`),
  ADD UNIQUE KEY `ID_NOM_2` (`ID_NOM`),
  ADD KEY `ID_NOM` (`ID_NOM`,`VACATION_TERM`);

--
-- Indices de la tabla `vacation_request`
--
ALTER TABLE `vacation_request`
  ADD PRIMARY KEY (`requestId`),
  ADD KEY `ID_NOM` (`ID_NOM`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `academic_attendance`
--
ALTER TABLE `academic_attendance`
  MODIFY `AttendanceId` int(15) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `academic_schedules`
--
ALTER TABLE `academic_schedules`
  MODIFY `PK` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `admin_attendance`
--
ALTER TABLE `admin_attendance`
  MODIFY `AttendanceId` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `admin_schedules`
--
ALTER TABLE `admin_schedules`
  MODIFY `schedulesID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `assigned_schedule`
--
ALTER TABLE `assigned_schedule`
  MODIFY `ASSIGNMENT_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `biometrictimeclock`
--
ALTER TABLE `biometrictimeclock`
  MODIFY `PK` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `code_accesslevels`
--
ALTER TABLE `code_accesslevels`
  MODIFY `CODE_LEVEL` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `code_ip`
--
ALTER TABLE `code_ip`
  MODIFY `ipID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `code_schedule`
--
ALTER TABLE `code_schedule`
  MODIFY `CODE_NOM` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `code_sesion`
--
ALTER TABLE `code_sesion`
  MODIFY `PK` int(3) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `groups_daytrip`
--
ALTER TABLE `groups_daytrip`
  MODIFY `PK` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `payroll_period`
--
ALTER TABLE `payroll_period`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vacation_anticipated`
--
ALTER TABLE `vacation_anticipated`
  MODIFY `PK` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `vacation_request`
--
ALTER TABLE `vacation_request`
  MODIFY `requestId` int(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
