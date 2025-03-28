<?php

$sql_get_teachers_b = "SELECT DISTINCT ACS.PK
    , ACS.PERSON_CODE_ID
    , ASE.LONG_DESC ACADEMIC_SESSION
    , ASE.CODE_VALUE_KEY CODE_SESSION
    , ACS.NAME DOC_NAME
    , ACS.SECTION
    , ACS.PROGRAM
    , ACS.PROGRAM_DESC
    , ACS.CURRICULUM
    , ACS.FORMAL_TITLE
    , ACS.BUILD_NAME_1
    , ACS.ROOM_NAME
    , ACS.CODE_DAY
    , ACS.GENERAL_ED
    , ACS.PUBLICATION_NAME_1
    , ACS.START_CLASS
    , ACS.DELAY_CLASS
    , ACS.MAX_DELAY_CLASS
    , ACS.MIN_END_CLASS
    , ACS.END_CLASS
    , (SELECT ACT.TINC FROM academic_attendance ACT WHERE ACT.SCHEDULE_ID = ACS.PK AND ACT.ATTENDANCE_DATE = DATE(NOW()) AND ACT.IN_OUT = 1 ORDER BY ACT.AttendanceId ASC LIMIT 1) ATTENDANCE_TINC
    , IFNULL( (SELECT CIN.DESCRIP_TINC FROM academic_attendance ACT INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ACT.TINC WHERE ACT.ATTENDANCE_DATE = DATE(NOW()) AND ACT.SCHEDULE_ID = ACS.PK AND ACT.IN_OUT = 1 ORDER BY ACT.AttendanceId ASC LIMIT 1), '') ATTENDANCE_STATUS 
    , IFNULL( (SELECT SUBSTRING( ACT.ATTENDANCE_TIME, 1, 8) FROM academic_attendance ACT WHERE ACT.ATTENDANCE_DATE = DATE(NOW()) AND ACT.SCHEDULE_ID = ACS.PK AND ACT.IN_OUT = 1 ORDER BY ACT.AttendanceId ASC LIMIT 1), '') ATTENDANCE_START_CLASS
    , IFNULL( (SELECT SUBSTRING( ACT.ATTENDANCE_TIME, 1, 8) FROM academic_attendance ACT WHERE ACT.ATTENDANCE_DATE = DATE(NOW()) AND ACT.SCHEDULE_ID = ACS.PK AND ACT.IN_OUT = 2 ORDER BY ACT.AttendanceId ASC LIMIT 1), '') ATTENDANCE_END_CLASS
    , IFNULL( (SELECT CONCAT(ACS2.PERSON_CODE_ID,' - ',ACS2.NAME) FROM academic_attendance ACT INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ACT.TINC INNER JOIN academic_schedules ACS2 ON ACS2.PERSON_CODE_ID = ACT.ACADEMIC_ID WHERE ACT.ATTENDANCE_DATE = DATE(NOW()) AND CIN.DESCRIP_TINC LIKE '%SUPLE%' AND ACT.SCHEDULE_ID = ACS.PK ORDER BY AttendanceId ASC LIMIT 1), '') TEACHER_CLASS
FROM academic_schedules ACS
INNER JOIN code_sesion_academic ASE ON ASE.CODE_VALUE_KEY = ACS.ACADEMIC_SESSION
INNER JOIN code_sesion CSE ON CSE.PK = ASE.CODE_NOM
WHERE DATE(NOW()) BETWEEN ACS.START_DATE AND ACS.END_DATE AND ACS.CODE_DAY = '$codeDay' AND ACS.PROGRAM = '$program'
    AND ASE.CODE_NOM = '$pkSesion'
ORDER BY ATTENDANCE_STATUS ASC, ACS.START_CLASS ASC";

$sql_get_teachers = "SELECT DISTINCT ACS.PK
    , ACS.PERSON_CODE_ID
    , ASE.LONG_DESC ACADEMIC_SESSION
    , ASE.CODE_VALUE_KEY CODE_SESSION
    , ACS.NAME DOC_NAME
    , ACS.SECTION
    , ACS.PROGRAM
    , ACS.PROGRAM_DESC
    , ACS.CURRICULUM
    , ACS.FORMAL_TITLE
    , ACS.BUILD_NAME_1
    , ACS.ROOM_NAME
    , ACS.CODE_DAY
    , ACS.GENERAL_ED
    , ACS.PUBLICATION_NAME_1
    , ACS.START_CLASS
    , ACS.DELAY_CLASS
    , ACS.MAX_DELAY_CLASS
    , ACS.MIN_END_CLASS
    , ACS.END_CLASS
    , (SELECT ACT.TINC FROM academic_attendance ACT WHERE ( ACS.PK = ACT.SCHEDULE_ID OR (ACS.PERSON_CODE_ID = ACT.ACADEMIC_ID AND ACS.CODE_DAY = ACT.CODE_DAY AND ACS.EVENT_ID = ACT.EVENT_ID AND ACS.PROGRAM = ACT.PROGRAM AND ACS.CURRICULUM = ACT.CURRICULUM AND ACT.ATTENDANCE_DATE BETWEEN ACS.START_DATE AND ACS.END_DATE) ) AND ACT.ATTENDANCE_DATE = '$selectedDate' AND ACT.IN_OUT = 1 ORDER BY ACT.AttendanceId ASC LIMIT 1) ATTENDANCE_TINC
    , IFNULL( (SELECT CIN.DESCRIP_TINC FROM academic_attendance ACT INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ACT.TINC WHERE ACT.ATTENDANCE_DATE = '$selectedDate' AND ( ACS.PK = ACT.SCHEDULE_ID OR (ACS.PERSON_CODE_ID = ACT.ACADEMIC_ID AND ACS.CODE_DAY = ACT.CODE_DAY AND ACS.EVENT_ID = ACT.EVENT_ID AND ACS.PROGRAM = ACT.PROGRAM AND ACS.CURRICULUM = ACT.CURRICULUM AND ACT.ATTENDANCE_DATE BETWEEN ACS.START_DATE AND ACS.END_DATE) ) AND ACT.IN_OUT = 1 ORDER BY ACT.AttendanceId ASC LIMIT 1), '') ATTENDANCE_STATUS 
    , IFNULL( (SELECT SUBSTRING( ACT.ATTENDANCE_TIME, 1, 8) FROM academic_attendance ACT WHERE ACT.ATTENDANCE_DATE = '$selectedDate' AND ( ACS.PK = ACT.SCHEDULE_ID OR (ACS.PERSON_CODE_ID = ACT.ACADEMIC_ID AND ACS.CODE_DAY = ACT.CODE_DAY AND ACS.EVENT_ID = ACT.EVENT_ID AND ACS.PROGRAM = ACT.PROGRAM AND ACS.CURRICULUM = ACT.CURRICULUM AND ACT.ATTENDANCE_DATE BETWEEN ACS.START_DATE AND ACS.END_DATE) ) AND ACT.IN_OUT = 1 ORDER BY ACT.AttendanceId ASC LIMIT 1), '') ATTENDANCE_START_CLASS
    , IFNULL( (SELECT SUBSTRING( ACT.ATTENDANCE_TIME, 1, 8) FROM academic_attendance ACT WHERE ACT.ATTENDANCE_DATE = '$selectedDate' AND ( ACS.PK = ACT.SCHEDULE_ID OR (ACS.PERSON_CODE_ID = ACT.ACADEMIC_ID AND ACS.CODE_DAY = ACT.CODE_DAY AND ACS.EVENT_ID = ACT.EVENT_ID AND ACS.PROGRAM = ACT.PROGRAM AND ACS.CURRICULUM = ACT.CURRICULUM AND ACT.ATTENDANCE_DATE BETWEEN ACS.START_DATE AND ACS.END_DATE) ) AND ACT.IN_OUT = 2 ORDER BY ACT.AttendanceId ASC LIMIT 1), '') ATTENDANCE_END_CLASS
    , IFNULL( (SELECT CONCAT(ACS2.PERSON_CODE_ID,' - ',ACS2.NAME) FROM academic_attendance ACT INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ACT.TINC INNER JOIN academic_schedules ACS2 ON ACS2.PERSON_CODE_ID = ACT.ACADEMIC_ID WHERE ACT.ATTENDANCE_DATE = '$selectedDate' AND CIN.DESCRIP_TINC LIKE '%SUPLE%' AND ( ACS.PK = ACT.SCHEDULE_ID OR (ACS.PERSON_CODE_ID = ACT.ACADEMIC_ID AND ACS.CODE_DAY = ACT.CODE_DAY AND ACS.EVENT_ID = ACT.EVENT_ID AND ACS.PROGRAM = ACT.PROGRAM AND ACS.CURRICULUM = ACT.CURRICULUM AND ACT.ATTENDANCE_DATE BETWEEN ACS.START_DATE AND ACS.END_DATE) ) ORDER BY AttendanceId ASC LIMIT 1), '') TEACHER_CLASS
    , IFNULL( (SELECT ACT.AttendanceId FROM academic_attendance ACT INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ACT.TINC INNER JOIN academic_schedules ACS2 ON ACS2.PERSON_CODE_ID = ACT.ACADEMIC_ID WHERE ACT.ATTENDANCE_DATE = '$selectedDate' AND CIN.DESCRIP_TINC LIKE '%SUPLE%' AND ( ACS.PK = ACT.SCHEDULE_ID OR (ACS.PERSON_CODE_ID = ACT.ACADEMIC_ID AND ACS.CODE_DAY = ACT.CODE_DAY AND ACS.EVENT_ID = ACT.EVENT_ID AND ACS.PROGRAM = ACT.PROGRAM AND ACS.CURRICULUM = ACT.CURRICULUM AND ACT.ATTENDANCE_DATE BETWEEN ACS.START_DATE AND ACS.END_DATE) ) ORDER BY AttendanceId ASC LIMIT 1), '') ID_SUSTITUTION
FROM academic_schedules ACS
INNER JOIN code_sesion_academic ASE ON ASE.CODE_VALUE_KEY = ACS.ACADEMIC_SESSION
INNER JOIN code_sesion CSE ON CSE.PK = ASE.CODE_NOM
WHERE '$selectedDate' BETWEEN ACS.START_DATE AND ACS.END_DATE AND ACS.CODE_DAY = '$selectedDay' AND ACS.PROGRAM = '$program'
    AND ASE.CODE_NOM = '$pkSesion'
ORDER BY ATTENDANCE_STATUS ASC, ACS.START_CLASS ASC";

$sql_teacher_attendance = "SELECT 
	DISTINCT
	CDY.NAME_DAY,
    AAT.AttendanceId,
    DOC.NAME NAME,
    CSA.LONG_DESC,
    AAT.ACADEMIC_ID,
    AAT.CURRICULUM,
    AAT.ROOM_ID,
    AAT.EVENT_ID,
    (SELECT ATE.CLASS_SUMMARY FROM academic_attendance ATE INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ATE.TINC WHERE ATE.AttendanceId = AAT.AttendanceId AND ATE.IN_OUT = 1) CLASS_SUMMARY,
    (SELECT CIN.DESCRIP_TINC FROM academic_attendance ATE INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ATE.TINC WHERE ATE.AttendanceId = AAT.AttendanceId AND ATE.IN_OUT = 1) INCIDENCE,
    (SELECT SUBSTRING( ATE.ATTENDANCE_TIME, 1, 8) FROM academic_attendance ATE WHERE ATE.AttendanceId = AAT.AttendanceId AND ATE.IN_OUT = 1) IN_TIME,
    (SELECT SUBSTRING( ATE.ATTENDANCE_TIME, 1, 8) FROM academic_attendance ATE WHERE ATE.AttendanceId = AAT.AttendanceId AND ATE.IN_OUT = 2) OUT_TIME,
    (SELECT ATE.JUSTIFY FROM academic_attendance ATE WHERE ATE.AttendanceId = AAT.AttendanceId AND ATE.IN_OUT = 1) JUSTIFY,
    (SELECT ATE.COMMENT FROM academic_attendance ATE WHERE ATE.AttendanceId = AAT.AttendanceId AND ATE.IN_OUT = 1) COMMENT
    FROM academic_attendance AAT
    INNER JOIN code_days CDY ON CDY.CODE_DAY = AAT.CODE_DAY
    INNER JOIN code_sesion_academic CSA ON CSA.CODE_VALUE_KEY = AAT.SESSION
    INNER JOIN academic_schedules DOC ON DOC.PERSON_CODE_ID = AAT.ACADEMIC_ID
    WHERE ATTENDANCE_DATE = '$selectedDay' AND AAT.PROGRAM = '$program' AND AAT.IN_OUT = '1'
    AND CSA.CODE_NOM = '$user_sesion'";

$sql_teacher_justify = "SELECT 
	DISTINCT
	CDY.NAME_DAY,
    AAT.AttendanceId,
    AAT.ATTENDANCE_DATE,
    DOC.NAME NAME,
    CSA.LONG_DESC,
    AAT.ACADEMIC_ID,
    AAT.CURRICULUM,
    DOC.FORMAL_TITLE,
    DOC.PROGRAM_DESC,
    DOC.PUBLICATION_NAME_1,
    DOC.ROOM_NAME,
    AAT.ROOM_ID,
    AAT.EVENT_ID,
    (SELECT ATE.CLASS_SUMMARY FROM academic_attendance ATE INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ATE.TINC WHERE ATE.SCHEDULE_ID = AAT.SCHEDULE_ID AND ATE.IN_OUT = 1 AND ATE.AttendanceId = AAT.AttendanceId ORDER BY ATE.AttendanceId ASC LIMIT 1) CLASS_SUMMARY,
    (SELECT CIN.DESCRIP_TINC FROM academic_attendance ATE INNER JOIN code_incidence CIN ON CIN.CODE_TINC = ATE.TINC WHERE ATE.SCHEDULE_ID = AAT.SCHEDULE_ID AND ATE.IN_OUT = 1 AND ATE.AttendanceId = AAT.AttendanceId ORDER BY ATE.AttendanceId ASC LIMIT 1) INCIDENCE,
    (SELECT SUBSTRING( ATE.ATTENDANCE_TIME, 1, 8) FROM academic_attendance ATE WHERE ATE.SCHEDULE_ID = AAT.SCHEDULE_ID AND ATE.IN_OUT = 1 AND ATE.AttendanceId = AAT.AttendanceId ORDER BY ATE.AttendanceId ASC LIMIT 1) IN_TIME,
    (SELECT SUBSTRING( ATE.ATTENDANCE_TIME, 1, 8) FROM academic_attendance ATE WHERE ATE.SCHEDULE_ID = AAT.SCHEDULE_ID AND ATE.IN_OUT = 2 AND ATE.AttendanceId = AAT.AttendanceId ORDER BY ATE.AttendanceId ASC LIMIT 1) OUT_TIME,
    (SELECT ATE.JUSTIFY FROM academic_attendance ATE WHERE ATE.SCHEDULE_ID = AAT.SCHEDULE_ID AND ATE.IN_OUT = 1 AND ATE.AttendanceId = AAT.AttendanceId ORDER BY ATE.AttendanceId ASC LIMIT 1) JUSTIFY,
    (SELECT ATE.COMMENT FROM academic_attendance ATE WHERE ATE.SCHEDULE_ID = AAT.SCHEDULE_ID AND ATE.IN_OUT = 1 AND ATE.AttendanceId = AAT.AttendanceId ORDER BY ATE.AttendanceId ASC LIMIT 1) COMMENT
    FROM academic_attendance AAT
    INNER JOIN code_days CDY ON CDY.CODE_DAY = AAT.CODE_DAY
    INNER JOIN code_sesion_academic CSA ON CSA.CODE_VALUE_KEY = AAT.SESSION
    INNER JOIN academic_schedules DOC ON DOC.PK = AAT.SCHEDULE_ID OR (DOC.PERSON_CODE_ID = AAT.ACADEMIC_ID AND DOC.CODE_DAY = AAT.CODE_DAY AND DOC.EVENT_ID = AAT.EVENT_ID AND DOC.PROGRAM = AAT.PROGRAM AND DOC.CURRICULUM = AAT.CURRICULUM AND AAT.ATTENDANCE_DATE BETWEEN DOC.START_DATE AND DOC.END_DATE)
    WHERE AAT.ATTENDANCE_DATE BETWEEN (SELECT START_DATE FROM payroll_period WHERE ID = '$selectedDay') AND (SELECT END_DATE FROM payroll_period WHERE ID = '$selectedDay')
        AND AAT.PROGRAM = '$program' AND AAT.IN_OUT = '1' AND JUSTIFY != ''
        AND CSA.CODE_NOM = '$pkSesion'
    ORDER BY AAT.AttendanceID ASC";

$sqlTeachersReport = "SELECT 
    DISTINCT
    CLD.CALENDAR_DATE
    , DYS.NAME_DAY
    , ASH.ACADEMIC_TERM
    , ASH.ACADEMIC_SESSION
    , ASH.PERSON_CODE_ID
    , ASH.PREV_GOV_ID
    , ASH.NAME ACA_NAME
    , ASH.PROGRAM
    , ASH.PROGRAM_DESC
    , ASH.CURRICULUM
    , ASH.FORMAL_TITLE
    , ASH.GENERAL_ED
    , ASH.SERIAL_ID
    , ASH.ROOM_ID
    , ASH.ROOM_NAME
    , ASH.EVENT_ID
    , ASH.PUBLICATION_NAME_1
    , ASH.SECTION
    , ASH.START_CLASS
    , ASH.END_CLASS
    , (SELECT SUBSTRING( AAT.ATTENDANCE_TIME,1,8) FROM academic_attendance AAT WHERE ( ASH.PK = AAT.SCHEDULE_ID OR (ASH.PERSON_CODE_ID = AAT.ACADEMIC_ID AND ASH.CODE_DAY = AAT.CODE_DAY AND ASH.EVENT_ID = AAT.EVENT_ID AND ASH.PROGRAM = AAT.PROGRAM AND ASH.CURRICULUM = AAT.CURRICULUM AND AAT.ATTENDANCE_DATE BETWEEN ASH.START_DATE AND ASH.END_DATE) ) AND AAT.ATTENDANCE_DATE = CLD.CALENDAR_DATE AND AAT.IN_OUT = '1' ORDER BY AAT.AttendanceId ASC LIMIT 1) CLASS_IN
    , (SELECT SUBSTRING( AAT.ATTENDANCE_TIME,1,8) FROM academic_attendance AAT WHERE ( ASH.PK = AAT.SCHEDULE_ID OR (ASH.PERSON_CODE_ID = AAT.ACADEMIC_ID AND ASH.CODE_DAY = AAT.CODE_DAY AND ASH.EVENT_ID = AAT.EVENT_ID AND ASH.PROGRAM = AAT.PROGRAM AND ASH.CURRICULUM = AAT.CURRICULUM AND AAT.ATTENDANCE_DATE BETWEEN ASH.START_DATE AND ASH.END_DATE) ) AND AAT.ATTENDANCE_DATE = CLD.CALENDAR_DATE AND AAT.IN_OUT = '2' ORDER BY AAT.AttendanceId ASC LIMIT 1) CLASS_OUT
    , (SELECT CIN.DESCRIP_TINC FROM academic_attendance AAT INNER JOIN code_incidence CIN ON CIN.CODE_TINC = AAT.TINC WHERE ( ASH.PK = AAT.SCHEDULE_ID OR (ASH.PERSON_CODE_ID = AAT.ACADEMIC_ID AND ASH.CODE_DAY = AAT.CODE_DAY AND ASH.EVENT_ID = AAT.EVENT_ID AND ASH.PROGRAM = AAT.PROGRAM AND ASH.CURRICULUM = AAT.CURRICULUM AND AAT.ATTENDANCE_DATE BETWEEN ASH.START_DATE AND ASH.END_DATE) ) AND AAT.ATTENDANCE_DATE = CLD.CALENDAR_DATE AND AAT.IN_OUT = '1' ORDER BY AAT.AttendanceId LIMIT 1) CLASS_INCIDENCE
    , IFNULL( (SELECT ACS2.NAME FROM academic_attendance AAT INNER JOIN code_incidence CIN ON CIN.CODE_TINC = AAT.TINC INNER JOIN academic_schedules ACS2 ON ACS2.PERSON_CODE_ID = AAT.ACADEMIC_ID WHERE AAT.ATTENDANCE_DATE = CLD.CALENDAR_DATE AND CIN.DESCRIP_TINC LIKE '%SUPLE%' AND AAT.SCHEDULE_ID = ASH.PK ORDER BY AttendanceId ASC LIMIT 1), '') TEACHER_CLASS
    FROM calendar CLD 
    INNER JOIN code_days DYS ON DYS.CODE_DAY = CLD.CODE_DAY
    INNER JOIN academic_schedules ASH ON ASH.CODE_DAY = CLD.CODE_DAY AND CLD.CALENDAR_DATE BETWEEN ASH.START_DATE AND ASH.END_DATE
    INNER JOIN code_sesion_academic SES ON SES.CODE_VALUE_KEY = ASH.ACADEMIC_SESSION
    INNER JOIN payroll_period PRT ON CLD.CALENDAR_DATE BETWEEN PRT.START_DATE AND PRT.END_DATE
    WHERE PRT.ID = '$payrollCode' AND SES.CODE_NOM = '$pkSesion'
    ORDER BY CLD.CALENDAR_DATE ASC";

$sqlIncidenceReport = "SELECT DISTINCT INCI.ID_PWC, INCI.NOM_ID, INCI.DOC_NAME, INCI.FLAG_CLINIC, INCI.PROGRAM, INCI.CURRICULUM
    , INCI.GENERAL_ED, INCI.ACADEMIC_SESSION, INCI.COUNTTINCS, INCI.COUNTSUPS
    FROM (
        -- Faltas injustificadas registradas
        (SELECT DISTINCT ASH.PERSON_CODE_ID AS ID_PWC, ASH.PREV_GOV_ID AS NOM_ID, ASH.NAME AS DOC_NAME, ASH.FLAG_CLINIC, ASH.PROGRAM
            , ASH.CURRICULUM, ASH.GENERAL_ED, ASH.EVENT_ID, ASH.ACADEMIC_SESSION, TINCS.COUNTTINCS, 0 COUNTSUPS
        FROM academic_schedules ASH
        LEFT OUTER JOIN (SELECT DISTINCT AAT.ACADEMIC_ID, AAT.SESSION, AAT.PROGRAM, AAT.CURRICULUM, AAT.GENERAL_ED, AAT.EVENT_ID, AAT.ROOM_ID, AAT.SECTION, AAT.SERIAL_ID, COUNT(AAT.TINC) COUNTTINCS
            FROM academic_attendance AAT
            INNER JOIN payroll_period PPE ON AAT.ATTENDANCE_DATE BETWEEN PPE.START_DATE AND PPE.END_DATE
            WHERE PPE.ID = '$payrollCode' AND AAT.TINC IN (1,2,3,4) AND AAT.IN_OUT = '1'
            GROUP BY AAT.ACADEMIC_ID, AAT.SESSION, AAT.PROGRAM, AAT.CURRICULUM, AAT.GENERAL_ED, AAT.EVENT_ID, AAT.ROOM_ID, AAT.SECTION, AAT.SERIAL_ID) TINCS ON TINCS.ACADEMIC_ID = ASH.PERSON_CODE_ID
                AND TINCS.SESSION = ASH.ACADEMIC_SESSION AND TINCS.PROGRAM = ASH.PROGRAM AND TINCS.CURRICULUM = ASH.CURRICULUM AND TINCS.GENERAL_ED = ASH.GENERAL_ED 
                AND TINCS.EVENT_ID = ASH.EVENT_ID AND TINCS.ROOM_ID = ASH.ROOM_ID AND TINCS.SECTION = ASH.SECTION AND TINCS.SERIAL_ID = ASH.SERIAL_ID
        WHERE TINCS.COUNTTINCS <> 0)
        -- Faltas no registradas
        UNION 
        (SELECT DISTINCT ASH.PERSON_CODE_ID AS ID_PWC, ASH.PREV_GOV_ID AS NOM_ID, ASH.NAME AS DOC_NAME, ASH.FLAG_CLINIC, ASH.PROGRAM
            , ASH.CURRICULUM, ASH.GENERAL_ED, ASH.EVENT_ID, ASH.ACADEMIC_SESSION, TINCS.COUNTTINCS, 0 COUNTSUPS
        FROM academic_schedules ASH
        LEFT OUTER JOIN (SELECT DISTINCT AAT.ACADEMIC_ID, AAT.SESSION, AAT.PROGRAM, AAT.CURRICULUM, AAT.GENERAL_ED, AAT.EVENT_ID, AAT.ROOM_ID, AAT.SECTION, AAT.SERIAL_ID, COUNT(AAT.TINC) COUNTTINCS
            FROM academic_attendance AAT
            INNER JOIN payroll_period PPE ON AAT.ATTENDANCE_DATE BETWEEN PPE.START_DATE AND PPE.END_DATE
            WHERE PPE.ID = '$payrollCode' AND AAT.TINC IN (26) AND AAT.IN_OUT = '1'
            GROUP BY AAT.ACADEMIC_ID, AAT.SESSION, AAT.PROGRAM, AAT.CURRICULUM, AAT.GENERAL_ED, AAT.EVENT_ID, AAT.ROOM_ID, AAT.SECTION, AAT.SERIAL_ID) TINCS ON  TINCS.SESSION = ASH.ACADEMIC_SESSION 
                AND TINCS.PROGRAM = ASH.PROGRAM AND TINCS.CURRICULUM = ASH.CURRICULUM AND TINCS.GENERAL_ED = ASH.GENERAL_ED 
                AND TINCS.EVENT_ID = ASH.EVENT_ID AND TINCS.ROOM_ID = ASH.ROOM_ID AND TINCS.SECTION = ASH.SECTION AND TINCS.SERIAL_ID = ASH.SERIAL_ID
        WHERE TINCS.COUNTTINCS <> 0)
        UNION 
        -- Obtenems sustituciones
        (SELECT DISTINCT ASH2.PERSON_CODE_ID AS ID_PWC, ASH2.PREV_GOV_ID AS NOM_ID, ASH2.NAME AS DOC_NAME, ASH.FLAG_CLINIC, ASH.PROGRAM
            , ASH.CURRICULUM, ASH.GENERAL_ED, ASH.EVENT_ID, ASH.ACADEMIC_SESSION, 0 COUNTTINCS, TINCS.COUNTTINCS COUNTSUPS
        FROM academic_schedules ASH
        LEFT OUTER JOIN (SELECT DISTINCT AAT.ACADEMIC_ID, AAT.SESSION, AAT.PROGRAM, AAT.CURRICULUM, AAT.GENERAL_ED, AAT.EVENT_ID, AAT.ROOM_ID, AAT.SECTION, AAT.SERIAL_ID, COUNT(AAT.TINC) COUNTTINCS
            FROM academic_attendance AAT
            INNER JOIN payroll_period PPE ON AAT.ATTENDANCE_DATE BETWEEN PPE.START_DATE AND PPE.END_DATE
            WHERE PPE.ID = '$payrollCode' AND AAT.TINC IN (26) AND AAT.IN_OUT = '1'
            GROUP BY AAT.ACADEMIC_ID, AAT.SESSION, AAT.PROGRAM, AAT.CURRICULUM, AAT.GENERAL_ED, AAT.EVENT_ID, AAT.ROOM_ID, AAT.SECTION, AAT.SERIAL_ID) TINCS ON  TINCS.SESSION = ASH.ACADEMIC_SESSION 
                AND TINCS.PROGRAM = ASH.PROGRAM AND TINCS.CURRICULUM = ASH.CURRICULUM AND TINCS.GENERAL_ED = ASH.GENERAL_ED 
                AND TINCS.EVENT_ID = ASH.EVENT_ID AND TINCS.ROOM_ID = ASH.ROOM_ID AND TINCS.SECTION = ASH.SECTION AND TINCS.SERIAL_ID = ASH.SERIAL_ID
        INNER JOIN academic_schedules ASH2 ON TINCS.ACADEMIC_ID = ASH2.PERSON_CODE_ID
        WHERE TINCS.COUNTTINCS <> 0)) AS INCI
    ORDER BY INCI.DOC_NAME ASC";
