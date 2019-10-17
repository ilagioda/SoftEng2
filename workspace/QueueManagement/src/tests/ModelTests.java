package tests;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import org.junit.Test;

import model.Model;
import model.Service;
import model.Counter;

import java.util.Arrays;
import java.util.HashSet;

public class ModelTests {

	@Test
	public void getNewTicketTest1() {
		Model m = new Model();
		Service ne = new Service("non_existing_name", "non_existing_code", 4);
		assertEquals(m.getNewTicket("Non_existing_name"), null);
	}
	
	@Test
	public void getNewTicketTest2() {
		Model m = new Model();
		Service s1 = new Service("Shipping", "S", 10);
		
		assertTrue(m.getNewTicket("Shipping").contains("less than 1 minute."));
		
	}
	
	@Test
	public void getNewTicketTest3() {
		Model m = new Model();
		int i;
		Service s1 = new Service("Shipping", "S", 10);
		
		for(i=0; i<6; ++i)
			m.getNewTicket("Shipping");
		
		//should be 30 min
		assertTrue(m.getNewTicket("Shipping").contains("30 minutes."));
		
	}
	
	@Test
	public void getNewTicketTest4() {
		Model m = new Model();
		int i;
		Service s1 = new Service("Shipping", "S", 10);
		
		for(i=0; i<13; ++i)
			m.getNewTicket("Shipping");
		
		//should be 65 min
		assertTrue(m.getNewTicket("Shipping").contains("1 hours and 5 minutes."));
	}
	
	@Test
	public void getNextTicketTest1() {
		Model m = new Model();
		m.getNextTicket(-1);
		assertEquals(m.getNextTicket(-1), null);
	}
	
	@Test
	public void getNextTicketTest2() {
		Model m = new Model();
		Service s1 = new Service("Shipping", "S", 10);
		Counter c = new Counter(0,new HashSet<Service>(Arrays.asList(s1)));
		assertEquals(m.getNextTicket(0), ""); //no tickets to serve for that service
	}
	
	@Test
	public void getNextTicketTest3() {
		Model m = new Model();
		Service s1 = new Service("Shipping", "S", 10);
		Counter c = new Counter(0,new HashSet<Service>(Arrays.asList(s1)));
		m.getNewTicket("Shipping");
		m.getNewTicket("Shipping");
		assertTrue(m.getNextTicket(0).contains("Ticket S1"));
	}
	
	@Test
	public void getNextTicketTest4() {
		Model m = new Model();
		Service s1 = new Service("Shipping", "S", 10);
		Counter c = new Counter(5,new HashSet<Service>());
		m.getNewTicket("Shipping");
		assertEquals(m.getNextTicket(5), null); //counter has no service associated
	}
	
}
