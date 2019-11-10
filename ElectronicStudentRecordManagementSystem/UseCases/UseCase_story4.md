# USE CASE - STORY#4

**Use Case**: enroll a student

**Scope:** admin's PC

**Level:** user-goal

**Intention in context:** the administrative officer has a list of students and he/she wants to enroll them by adding their information in the system's database

**Primary actor:** administrative officer (admin)

**Secondary/Support actor(s):** none

**Stakeholders' Interests:**
- Parent: wants his/her child to be enrolled so that he/she can monitor everything concerning his/her school performance
- Teacher: needs to have a list of enrolled students who attend his/her lectures
- Administrative officer: needs to have a list of enrolled students in order to perform other actions in the system
- Principal: needs to have a list of enrolled students so that he/she can form classes 

**Precondition:**
- The system must have a database already configured
- The admin must have a list of students that have to be enrolled

**Minimum Guarantees:** none

**Success Guarantees:** the information about the student and his/her parents is correctly inserted in the database

**Trigger:** none, because it’s the administrative officer himself/herself that starts the interaction

**Main Success Scenario:**
1. The system asks for information about the student
2. The admin inserts the information about the student
3. The system asks for information about the parents
4. The admin inserts the information about the parents
5. The system asks for confirmation
6. The admin confirms
7. The system confirms with a message that the operation has been completed successfully 

The use case terminates with success

**Extensions:**

2a. The admin inserts the wrong data in the wrong form's places: the system notifies the admin and the use case terminates in failure

4a. The admin inserts the wrong data in the wrong form's places: the system notifies the admin and the use case terminates in failure

6a. The admin cancels: the use case terminates in failure 

7a. The system is not able to connect to the database: the use case terminates in failure 

7b. The system is not able to execute the queries on the database: the use case terminates in failure 


