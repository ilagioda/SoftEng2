# USE CASE - STORY#5

**Use Case**: 
- Enter the class composition in the system

**Scope:** 
- Admin's PC

**Level:** 
- User-goal

**Intention in context:** 
- The administrative officer selects a class sees the composition and enters it into the system's database by simply pressing a button.

**Primary actor:** administrative officer (admin)

**Secondary/Support actor(s):** none

**Stakeholders' Interests:**
- Parent: would like to see in which class his/her child was inserted
- Teacher: wants to see the composition of a particular class 
- Administrative officer:  none?? <----------??----------> 
- Principal:  none?? <----------??----------> 

**Precondition:**
- The system must have a database already configured
- The system should already have the proposed class compositions

**Minimum Guarantees:** 
- The class composition is accepted and the information saved

**Success Guarantees:** 
- TThe students once the class composition is accepted must be enrolled in that particular class

**Trigger:** 
- None, because it’s the administrative officer that starts the interaction

**Main Success Scenario:**
1. The admin selects a particular class for which he/she wants to see the proposed class composition
2. The system shows the proposed class composition for the selected class
3. The admin accepts the class composition by simply clicking a button 
4. The system updates all the informations related in the database
5. The system confirms with a message that the operation has been completed successfully 

The use case terminates with success

**Extensions:**

3a. The admin doesn't accept the class composition: the system must not change the class compositions and the info related to the students 

2a. The system is not able to connect to the database: the use case terminates in failure 

4b. The system is not able to execute the queries on the database: the use case terminates in failure 


