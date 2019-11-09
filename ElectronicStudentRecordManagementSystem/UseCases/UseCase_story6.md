# USE CASE - STORY#6

**Use Case**: assign a grade to a student

**Scope:** teacher's PC

**Level:** user-goal

**Intention in context:** the teacher wants to assign a grade to a student, adding the information to the database

**Primary actor:** teacher

**Secondary/Support actor(s):** none

**Stakeholders' Interests:**
- Parent: wants to know the grades of his/her child
- Teacher: needs to have all the grades of his/her students for the future (e.g. at the end of semesters)
- Principal: needs to have all the grades of the school students

**Precondition:**
- The system must have a database already configured
- There must be a list of students already assigned to their class

**Minimum Guarantees:** the information in the database remains correct and coherent

**Success Guarantees:** the information about the grade is correctly inserted in the database

**Trigger:** none, it’s the teacher that starts the interaction

**Main Success Scenario:**
1. The system asks for which class the teacher wants to enter the grade (if he/she has more than one class)
2. The teacher inserts the class for which he wants to enter the grade
3. The system asks for which subject the teacher wants to enter the grade (if he/she teaches more than one subject)
4. The teacher inserts the subject for which he wants to enter the grade
5. The system asks information about the student, the date and the hour of the grade
6. The teacher inserts the required information
7. The system checks if the operation is valid
8. Tye system validates the operation

The use case terminates with success

**Extensions:**

5a. The teacher inserts the wrong date (it must be on the same week): the use case terminates in failure

6a. The teacher cancels: the use case terminates in failure 

8a. The system is not able to connect to the database: the use case terminates in failure 

8b. The system is not able to execute the queries on the database: the use case terminates in failure 


