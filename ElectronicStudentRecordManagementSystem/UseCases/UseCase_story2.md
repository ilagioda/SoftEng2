**Use case**: record daily lecture topics
**Scope**: teacher's PC
**Level**: user-goal
**Intention in context**: the teacher wants to record the daily lecture topics adding the information to the DB
**Primary actor**: teacher
**Secondary/Support actor(s)**: none
**Stakeholders' Interests**:
- Parent: wants to be informed about the daily lecture topics
- Teacher: needs to record the daily lecture topics for institutional purposes
- Principal: needs to have an official recording for institutional purposes

**Precondition**:
- The system must have a database already configured
- The teacher must be registered in the system
- The teacher must have already taken a lesson

**Minimum Guarantees**: none
**Success Guarantees**: the information is correctly saved in the database
**Trigger**: none, the teacher starts the interaction
**Main Success Scenario**:
1. The system asks the class, the subject, the date, the hour and the lecture topic(s) for which the teacher wants to record 
2. The teacher selects the class, the subject, the date and the hour and inserts the lecture topic(s)
3. The teacher confirms the data
4. The system checks the validity of the information 
5. The system validates the operation
6. The system saves the information inside the database
The use case terminates with success

**Extensions**:
2a. The teacher selects the wrong date: he use case terminates in failure
5a. The system is not able to connect to the database: the use case terminates in failure
5b. The system is not able to execute the queries on the database: the use case terminates in failure
