package tests;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import org.junit.Test;

import model.Model;
import model.Service;
import model.Counter;
import java.util.HashSet;

public class ModelTests {

	@Test
	public void getNewTicketTest1() {
		Model m = new Model();
		Service ne = new Service("non_existing_name", "non_existing_code", 4);
		assertEquals(m.getNewTicket(ne), "");
	}
	
	@Test
	public void getNewTicketTest2() {
		Model m = new Model();
		Service s1 = new Service("Shipping", "S", 10);
		
		assertTrue(m.getNewTicket(s1).contains("less than 1 minute."));
		
	}
	
	@Test
	public void getNewTicketTest3() {
		Model m = new Model();
		int i;
		Service s1 = new Service("Shipping", "S", 10);
		
		for(i=0; i<6; ++i)
			m.getNewTicket(s1);
		
		//should be 30 min
		assertTrue(m.getNewTicket(s1).contains("30 minutes."));
		
	}
	
	@Test
	public void getNewTicketTest4() {
		Model m = new Model();
		int i;
		Service s1 = new Service("Shipping", "S", 10);
		
		for(i=0; i<13; ++i)
			m.getNewTicket(s1);
		
		//should be 65 min
		assertTrue(m.getNewTicket(s1).contains("1 hours and 5 minutes."));
	}
	
}
