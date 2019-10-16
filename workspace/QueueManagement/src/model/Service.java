package model;

public class Service {
	
	Service(String name, int waitTime){
		this.name=name;
		this.waitTime=waitTime;
	}
	
	private String name;
	private int waitTime;
	
	
	public void setName(String name) {
		this.name = name;
	}
	public void setWaitTime(int waitTime) {
		this.waitTime = waitTime;
	}
	public int getWaitTime() {
		return this.waitTime;
	}
	public String getName() {
		return this.name;	
	}
}

