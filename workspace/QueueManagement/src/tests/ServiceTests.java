package tests;

import static org.junit.Assert.assertEquals;

import java.util.HashSet;

import org.junit.Test;

import model.Counter;
import model.Service;

public class ServiceTests {
	
	@Test
	public void getNextTicketIdTest2() {
		Service a = new Service("nameA", "codeA", 1);
		assertEquals(2, a.getNextTicketId());
	}
	
	//GETTERS AND SETTERS, NO NEED TO BE TESTED
	
}
