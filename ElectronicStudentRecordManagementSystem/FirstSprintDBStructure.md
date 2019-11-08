Parent - **E-mail**, hashedPassword*, salt*, name, surname, CodFisc, FirstLogin

Student - **CodFisc**, emailP1, emailP2*, ClassID*

Teacher - **CodFisc**, hashedPassword, salt

Principal - **CodFisc**, hashedPassword, salt

Admin - **CodFisc**, hashedPassword, salt


Marks - **CodFiscStudent**, **Subject**, **Date**, **Hour**, mark

Lecture - **Date**, **Hour**, **ClassID**, CodFiscTeacher, Subject, Topic

Assignments - **Subject**, **Date**, **ClassID**, textAssignment


